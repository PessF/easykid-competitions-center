<?php

namespace App\Services;

use Google_Client;
use Google_Service_Sheets;
use Google_Service_Drive;
use Google_Service_Drive_DriveFile;
use Google_Service_Sheets_ValueRange;
use Google_Service_Sheets_BatchUpdateSpreadsheetRequest;
use Google_Service_Sheets_Request;
use Exception;

class GoogleSheetService
{
    protected $client;
    protected $sheetsService;
    protected $driveService;

    public function __construct()
    {
        $this->client = new Google_Client();
        $this->client->setApplicationName('Easykids Registration System');
        
        $this->client->setScopes([
            Google_Service_Sheets::SPREADSHEETS,
            Google_Service_Drive::DRIVE
        ]);
        
        // 🚀 แก้ไข: เปลี่ยนมาใช้ตัวแปรชุด GOOGLE_DRIVE_ เพื่อให้ตรงกับโปรเจกต์ที่มีพื้นที่ 2TB
        $this->client->setClientId(env('GOOGLE_DRIVE_CLIENT_ID'));
        $this->client->setClientSecret(env('GOOGLE_DRIVE_CLIENT_SECRET'));
        $this->client->setAccessType('offline');
        
        $refreshToken = env('GOOGLE_DRIVE_REFRESH_TOKEN');
        if (!$refreshToken) {
            throw new Exception("ไม่พบตัวแปร GOOGLE_DRIVE_REFRESH_TOKEN ในไฟล์ .env");
        }
        $this->client->refreshToken($refreshToken);

        $this->sheetsService = new Google_Service_Sheets($this->client);
        $this->driveService = new Google_Service_Drive($this->client);
    }

    public function processAutomation($rowData, $headers, $fileName, $tabName, $folderId, $adminEmail = null)
    {
        try {
            $spreadsheetId = $this->getSpreadsheetId($fileName, $folderId);

            // คืนค่าข้อมูลแท็บ (ถ้าสร้างใหม่ จะมีการลบ Sheet1 ออกด้วย)
            $isNewTab = $this->ensureTabExists($spreadsheetId, $tabName);

            if ($isNewTab) {
                $headerBody = new Google_Service_Sheets_ValueRange(['values' => [$headers]]);
                $this->sheetsService->spreadsheets_values->append($spreadsheetId, $tabName . '!A1', $headerBody, ['valueInputOption' => 'USER_ENTERED']);
                
                $this->formatHeader($spreadsheetId, $tabName);
            }

            $body = new Google_Service_Sheets_ValueRange(['values' => [$rowData]]);
            $params = ['valueInputOption' => 'USER_ENTERED'];
            $range = $tabName . '!A:A';

            $this->sheetsService->spreadsheets_values->append($spreadsheetId, $range, $body, $params);
            
            return $spreadsheetId;
        } catch (Exception $e) {
            // ถ้าพังเพราะ API ยังไม่เปิด จะพ่น Error บอกให้ไปกด Enable ทันที
            throw new Exception("Automation Failed: " . $e->getMessage());
        }
    }

    protected function getSpreadsheetId($name, $folderId)
    {
        $query = "name = '$name' and '$folderId' in parents and mimeType = 'application/vnd.google-apps.spreadsheet' and trashed = false";
        $response = $this->driveService->files->listFiles(['q' => $query]);
        
        if (count($response->getFiles()) > 0) {
            return $response->getFiles()[0]->getId();
        }

        $fileMetadata = new Google_Service_Drive_DriveFile([
            'name' => $name,
            'mimeType' => 'application/vnd.google-apps.spreadsheet',
            'parents' => [$folderId]
        ]);
        
        $file = $this->driveService->files->create($fileMetadata, ['fields' => 'id']);
        return $file->id;
    }

    protected function ensureTabExists($spreadsheetId, $tabName)
    {
        $spreadsheet = $this->sheetsService->spreadsheets->get($spreadsheetId);
        $sheets = $spreadsheet->getSheets();
        
        $hasTab = false;
        $sheet1Id = null;

        foreach ($sheets as $sheet) {
            $title = $sheet->getProperties()->getTitle();
            if ($title === $tabName) {
                $hasTab = true;
            }
            if ($title === 'Sheet1') {
                $sheet1Id = $sheet->getProperties()->getSheetId();
            }
        }

        if (!$hasTab) {
            // 1. สร้างแท็บใหม่
            $requests = [
                new Google_Service_Sheets_Request([
                    'addSheet' => ['properties' => ['title' => $tabName]]
                ])
            ];

            // 2. ถ้ามี Sheet1 อยู่ ให้สั่งลบทิ้งไปพร้อมกันเลย
            if ($sheet1Id !== null) {
                $requests[] = new Google_Service_Sheets_Request([
                    'deleteSheet' => ['sheetId' => $sheet1Id]
                ]);
            }

            $body = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest(['requests' => $requests]);
            $this->sheetsService->spreadsheets->batchUpdate($spreadsheetId, $body);
            
            return true;
        }

        return false;
    }

    protected function formatHeader($spreadsheetId, $tabName)
    {
        $spreadsheet = $this->sheetsService->spreadsheets->get($spreadsheetId);
        $sheetIdToFormat = null;
        
        foreach ($spreadsheet->getSheets() as $sheet) {
            if ($sheet->getProperties()->getTitle() === $tabName) {
                $sheetIdToFormat = $sheet->getProperties()->getSheetId();
                break;
            }
        }

        if ($sheetIdToFormat === null) return;

        $requests = [
            new Google_Service_Sheets_Request([
                'repeatCell' => [
                    'range' => [
                        'sheetId' => $sheetIdToFormat,
                        'startRowIndex' => 0,
                        'endRowIndex' => 1,
                    ],
                    'cell' => [
                        'userEnteredFormat' => [
                            'backgroundColor' => ['red' => 0.85, 'green' => 0.93, 'blue' => 1.0], 
                            'textFormat' => ['bold' => true],
                            'horizontalAlignment' => 'CENTER'
                        ]
                    ],
                    'fields' => 'userEnteredFormat(backgroundColor,textFormat,horizontalAlignment)'
                ]
            ]),
            new Google_Service_Sheets_Request([
                'updateSheetProperties' => [
                    'properties' => [
                        'sheetId' => $sheetIdToFormat,
                        'gridProperties' => ['frozenRowCount' => 1]
                    ],
                    'fields' => 'gridProperties.frozenRowCount'
                ]
            ])
        ];

        $batchUpdateRequest = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest(['requests' => $requests]);
        $this->sheetsService->spreadsheets->batchUpdate($spreadsheetId, $batchUpdateRequest);
    }
}
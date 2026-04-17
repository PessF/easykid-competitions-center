@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-white/10 bg-black text-white placeholder-gray-500 focus:border-blue-500 focus:ring-blue-500 rounded-xl shadow-sm transition-all duration-200']) }}>
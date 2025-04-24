@props(['items' => []])

<nav class="text-sm mb-3 mt-2">
    <ol class="flex flex-wrap items-center space-x-1 md:space-x-2">
        @foreach($items as $index => $item)
            <li class="flex items-center">
                @if(!$loop->first)
                    <span class="mx-1 text-gray-400 dark:text-gray-500">/</span>
                @endif
                
                @if(isset($item['route']) && !$loop->last)
                    <a href="{{ route($item['route']) }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                        {{ $item['label'] }}
                    </a>
                @else
                    <span class="@if($loop->last) text-gray-600 dark:text-gray-300 @else text-gray-500 dark:text-gray-400 @endif">
                        {{ $item['label'] }}
                    </span>
                @endif
            </li>
        @endforeach
    </ol>
</nav> 
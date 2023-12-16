@props(['disabled' => false, 'withicon' => false])

<select {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge([
    'class' => 'block w-full py-2 border-gray-400 rounded-md focus:border-gray-400 focus:ring focus:ring-orange-500 focus:ring-offset-2 focus:ring-offset-white dark:border-gray-600 dark:bg-dark-eval-1 dark:text-gray-300 dark:focus:ring-offset-dark-eval-1 ' . ($withicon ? 'pl-11 pr-4' : 'px-4'),
]) !!}>
    {{ $slot }}
</select>

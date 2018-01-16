@if($menuItem && count($menuItem->children))
    <ul class="menu">
        @foreach($menuItem->children as $child)
            <li class="menu__item">
                <a href="{{ url($child->url ? $child->url : '/') }}">{{ $child->title }}</a>
                {!! crud_menu($child->id) !!}
            </li>
        @endforeach
    </ul>
@endif
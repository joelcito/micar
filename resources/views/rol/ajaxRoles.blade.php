@php
    $menus = json_decode($rol->menus);
    $menusPer = json_decode($rol->permisos);
@endphp
<form id="formulario_roles_permisos">
    <div class="row">
        <div class="col-md-8">
            <ul>
                @foreach($menus as $menu)
                        <li>
                            <input type="checkbox" {{ ($menu->estado)? 'checked' : '' }} name="menu_{{ $menu->id }}" id="menu_{{ $menu->id }}"> {{ $menu->name }}
                            @if(isset($menu->children) && count($menu->children))
                                <ul>
                                    @foreach($menu->children as $child)
                                            <li>
                                                <input type="checkbox" name="child_{{ $child->id }}" id="child_{{ $child->id }}" {{ ($child->estado)? 'checked' : '' }}> {{ $child->name }}
                                            </li>
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                @endforeach
            </ul>
            <input type="hidden" value="{{ $rol->id }}" name="rol_id">
        </div>
        <div class="col-md-4">
            <ul>
                <li><input type="checkbox" name="{{ $menusPer[0]->name }}" {{ ($menusPer[0]->estado)? 'checked' : '' }}> {{ $menusPer[0]->name }}</li>
                <li><input type="checkbox" name="{{ $menusPer[1]->name }}" {{ ($menusPer[1]->estado)? 'checked' : '' }}> {{ $menusPer[1]->name }}</li>
            </ul>
        </div>
    </div>
</form>


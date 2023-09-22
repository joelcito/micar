<!--begin::Table-->
    <table class="table align-middle table-row-dashed fs-6 gy-5" id="tabla_user">
        <thead>
            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                <th class="min-w-125px">Nombre</th>
                <th class="min-w-125px">Ape. Paterno</th>
                <th class="min-w-125px">Ape. Materno</th>
                <th class="min-w-125px">Cedula</th>
                <th class="min-w-125px">Correo</th>
                <th class="min-w-125px">Rol</th>
                <th class="text-end min-w-100px">Actions</th>
            </tr>
        </thead>
        <tbody class="text-gray-600 fw-semibold">
            @forelse ( $usuarios as  $u )
                <tr>
                    <td class="d-flex align-items-center">
                        <div class="d-flex flex-column">
                            <a class="text-gray-800 text-hover-primary mb-1">{{ $u->nombres }}</a>
                        </div>
                    </td>
                    <td>
                        <a class="text-gray-800 text-hover-primary mb-1">{{ $u->ap_paterno }}</a>
                    </td>
                    <td>
                        <a class="text-gray-800 text-hover-primary mb-1">{{ $u->ap_materno }}</a>
                    </td>
                    <td>
                        <a class="text-gray-800 text-hover-primary mb-1">{{ $u->cedula }}</a>
                    </td>
                    <td>
                        <span class="badge badge-light-success fw-bold">{{ $u->email }}</span>
                    </td>
                    <td>
                        <span class="badge badge-primary fw-bold">{{ $u->rol->nombre }}</span>
                    </td>
                    <td class="text-end">
                        <a href="{{ url('user/detalle',[$u->id]) }}" class="btn btn-icon btn-info btn-sm"><i class="fa fa-eye"></i></a>
                    </td>
                </tr>
            @empty
                <h4 class="text-danger text-center">Sin registros</h4>
            @endforelse

            {{--  <tr>
                <td>
                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                        <input class="form-check-input" type="checkbox" value="1" />
                    </div>
                </td>
                <td class="d-flex align-items-center">
                    <!--begin:: Avatar -->
                    <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                        <a href="../../demo1/dist/apps/user-management/users/view.html">
                            <div class="symbol-label">
                                <img src="assets/media/avatars/300-6.jpg" alt="Emma Smith" class="w-100" />
                            </div>
                        </a>
                    </div>
                    <!--end::Avatar-->
                    <!--begin::User details-->
                    <div class="d-flex flex-column">
                        <a href="../../demo1/dist/apps/user-management/users/view.html" class="text-gray-800 text-hover-primary mb-1">Emma Smith</a>
                        <span>smith@kpmg.com</span>
                    </div>
                    <!--begin::User details-->
                </td>
                <td>Administrator</td>
                <td>
                    <div class="badge badge-light fw-bold">Yesterday</div>
                </td>
                <td></td>
                <td>22 Sep 2023, 11:05 am</td>
                <td class="text-end">
                    <a href="#" class="btn btn-light btn-active-light-primary btn-flex btn-center btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                    <i class="ki-duotone ki-down fs-5 ms-1"></i></a>
                    <!--begin::Menu-->
                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                        <!--begin::Menu item-->
                        <div class="menu-item px-3">
                            <a href="../../demo1/dist/apps/user-management/users/view.html" class="menu-link px-3">Edit</a>
                        </div>
                        <!--end::Menu item-->
                        <!--begin::Menu item-->
                        <div class="menu-item px-3">
                            <a href="#" class="menu-link px-3" data-kt-users-table-filter="delete_row">Delete</a>
                        </div>
                        <!--end::Menu item-->
                    </div>
                    <!--end::Menu-->
            </tr>
            <tr>
                <td>
                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                        <input class="form-check-input" type="checkbox" value="1" />
                    </div>
                </td>
                <td class="d-flex align-items-center">
                    <!--begin:: Avatar -->
                    <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                        <a href="../../demo1/dist/apps/user-management/users/view.html">
                            <div class="symbol-label fs-3 bg-light-danger text-danger">M</div>
                        </a>
                    </div>
                    <!--end::Avatar-->
                    <!--begin::User details-->
                    <div class="d-flex flex-column">
                        <a href="../../demo1/dist/apps/user-management/users/view.html" class="text-gray-800 text-hover-primary mb-1">Melody Macy</a>
                        <span>melody@altbox.com</span>
                    </div>
                    <!--begin::User details-->
                </td>
                <td>Analyst</td>
                <td>
                    <div class="badge badge-light fw-bold">20 mins ago</div>
                </td>
                <td>
                    <div class="badge badge-light-success fw-bold">Enabled</div>
                </td>
                <td>25 Jul 2023, 11:30 am</td>
                <td class="text-end">
                    <a href="#" class="btn btn-light btn-active-light-primary btn-flex btn-center btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                    <i class="ki-duotone ki-down fs-5 ms-1"></i></a>
                    <!--begin::Menu-->
                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                        <!--begin::Menu item-->
                        <div class="menu-item px-3">
                            <a href="../../demo1/dist/apps/user-management/users/view.html" class="menu-link px-3">Edit</a>
                        </div>
                        <!--end::Menu item-->
                        <!--begin::Menu item-->
                        <div class="menu-item px-3">
                            <a href="#" class="menu-link px-3" data-kt-users-table-filter="delete_row">Delete</a>
                        </div>
                        <!--end::Menu item-->
                    </div>
                    <!--end::Menu-->
                </td>
            </tr>  --}}

        </tbody>
    </table>
<!--end::Table-->
    <script>
        $('#tabla_user').DataTable({
            {{--  responsive: true,
            language: {
                url: '{{ asset('datatableEs.json') }}',
            },
            order: [[ 0, "desc" ]]  --}}
        });
    </script>

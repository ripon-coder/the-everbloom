@extends('admin.partials.app',['title'=>'Attribute Values'])
@section('content')
    <div class="app-content pt-3 p-md-3 p-lg-4">
        <div class="container-xl">

            <div class="row g-3 mb-4 align-items-center justify-content-between">
                <div class="col-auto">
                    <h1 class="app-page-title mb-0">Attribute Values</h1>
                </div>
                <div class="col-auto">
                    <div class="page-utilities">
                        <div class="row g-2 justify-content-start justify-content-md-end align-items-center">
                            <div class="col-auto">
                                <form class="table-search-form row gx-1 align-items-center">
                                    <div class="col-auto">
                                        <input type="text" id="search-orders" name="searchorders"
                                            class="form-control search-orders" placeholder="Search">
                                    </div>
                                    <div class="col-auto">
                                        <button type="submit" class="btn app-btn-secondary">Search</button>
                                    </div>
                                </form>
                            </div><!--//col-->
                            <div class="col-auto">
                                <select class="form-select w-auto">
                                    <option selected value="option-1">All</option>
                                    <option value="option-2">This week</option>
                                    <option value="option-3">This month</option>
                                    <option value="option-4">Last 3 months</option>
                                </select>
                            </div>
                            <div class="col-auto">
                                <a class="btn app-btn-secondary" href="{{ route('admin.attribute-value.create') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em"
                                        fill="currentColor" class="bi bi-plus-circle me-1" viewBox="0 0 16 16">
                                        <path d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zM8 0a8 8 0 1 1 0 16A8 8 0 0 1 8 0z" />
                                        <path
                                            d="M8 4a.5.5 0 0 1 .5.5V7.5H11.5a.5.5 0 0 1 0 1H8.5V11.5a.5.5 0 0 1-1 0V8.5H4.5a.5.5 0 0 1 0-1H7.5V4.5A.5.5 0 0 1 8 4z" />
                                    </svg>
                                    New Attribute Value
                                </a>
                            </div>
                        </div><!--//row-->
                    </div><!--//table-utilities-->
                </div><!--//col-auto-->
            </div><!--//row-->
            <div class="app-card app-card-orders-table shadow-sm mb-5">
                <div class="app-card-body">
                    <div class="table-responsive">
                        <table class="table app-table-hover mb-0 text-left">
                            <thead>
                                <tr>
                                    <th class="cell">ID</th>
                                    <th class="cell">Attribute</th>
                                    <th class="cell">Value</th>
                                    <th class="cell">Date</th>
                                    <th class="cell">Status</th>
                                    <th class="cell">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($attributeValues as $item)
                                    <tr>
                                        <td class="cell">{{ $item->id }}</td>
                                        <td class="cell"><span class="truncate">{{ optional($item->attribute)->name }}</span></td>
                                        <td class="cell"><span class="truncate">{{ $item->value }}</span></td>
                                        <td class="cell">{{ $item->created_at }}</td>
                                        <td class="cell"><span class="badge {{$item->status->badgeClass()}}">{{$item->status->label()}}</span></td>
                                        <td class="cell">
                                            <a class="btn-sm app-btn-secondary" href="{{route('admin.attribute-value.edit',$item)}}">Edit</a>
                                            <a onclick="if(confirm('Are you sure want to delete?'))document.getElementById('delete_{{$item->id}}').submit()" class="btn-sm app-btn-danger" href="#">Delete</a>
                                        </td>
                                    </tr>
                                    <form id="delete_{{$item->id}}" class="d-none" action="{{route('admin.attribute-value.destroy',$item)}}" method="POST">@csrf @method('DELETE')</form>
                                @endforeach
                            </tbody>
                        </table>
                    </div><!--//table-responsive-->
                </div><!--//app-card-body-->
            </div><!--//app-card-->
            <nav class="app-pagination">
                <ul class="pagination justify-content-center">
                    {{ $attributeValues->links('pagination::bootstrap-4') }}
                </ul>
            </nav><!--//app-pagination-->
        </div><!--//container-fluid-->
    </div><!--//app-content-->
@endsection

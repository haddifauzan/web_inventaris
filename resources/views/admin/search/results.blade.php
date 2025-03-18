@extends('admin.layouts.master')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Search Results for "{{ $query }}"</h4>
                    <div class="float-end">
                        <form method="GET" action="{{ route('search') }}" class="form-inline">
                            <div class="input-group">
                                <select name="category" class="form-select" onchange="this.form.submit()">
                                    <option value="all" {{ $category == 'all' ? 'selected' : '' }}>All Categories</option>
                                    <option value="barang" {{ $category == 'barang' ? 'selected' : '' }}>Barang</option>
                                    <option value="departemen" {{ $category == 'departemen' ? 'selected' : '' }}>Departemen</option>
                                    <option value="lokasi" {{ $category == 'lokasi' ? 'selected' : '' }}>Lokasi</option>
                                    <option value="ip" {{ $category == 'ip' ? 'selected' : '' }}>IP Address</option>
                                    <option value="menuaktif" {{ $category == 'menuaktif' ? 'selected' : '' }}>Menu Aktif</option>
                                    <option value="tipebarang" {{ $category == 'tipebarang' ? 'selected' : '' }}>Tipe Barang</option>
                                </select>
                                <input type="text" name="query" value="{{ $query }}" class="form-control">
                                <button type="submit" class="btn btn-primary">Search</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($results) > 0)
                                    @foreach($results as $result)
                                        <tr>
                                            <td>{{ $result['type'] }}</td>
                                            <td>{{ $result['title'] }}</td>
                                            <td>{{ $result['description'] }}</td>
                                            <td>
                                                @if($result['route'])
                                                    <a href="{{ route($result['route'], $result['route_params']) }}" class="btn btn-sm btn-primary">View</a>
                                                @else
                                                    <span class="text-muted">No action</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4" class="text-center">No results found</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@extends('layouts.layout', ['page' => 'Environments'])

@section('content')

	<a href="{{ url('environments/create') }}" class="btn btn-default create">Create new environment</a>
	<div class="table-responsive">
		<table class="table-responsive table table-hover">
			<h1>Environments</h1>
			<thead>
				<tr>
					<th>Name</th>
					<th>Subdomain</th>
					<th>Company</th>
					<th>Accounts</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>
				@foreach($environments as $environment)
					<tr>
						<td>{{ $environment->name }}</td>
						<td>{{ $environment->subdomain }}</td>
						<td>{{ $environment->company->name ?? '' }}</td>
						<td><a href="{{ url('/environments/' . $environment->id . '/users') }}" class="btn btn-default">Accounts</a></td>
						<td>
							<a href="{{ url('/environments/' . $environment->id . '/edit') }}"><i class="material-icons">edit</i></a>
							<a href="{{ url('/environments/' . $environment->id . '/delete') }}"><i class="material-icons">delete</i></a>
						</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>

@stop

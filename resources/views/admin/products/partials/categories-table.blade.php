				<div class="table-responsive">
					<table id="category-table" class="datatable table table-striped table-bordered table-hover table-center mb-0">
						<thead>
							<tr style="boder:1px solid black;">
								<th>Name</th>
								<th>Created date</th>
								<th class="text-center action-btn">Actions</th>
							</tr>
						</thead>
						<tbody>
							@foreach($categories as $category)
								<tr>
									<td>{{ $category->name }}</td>
									<td>{{ date_format(date_create($category->created_at),"d M,Y") }}</td>
									<td class="text-center">
										<a data-id="{{ $category->id }}" data-name="{{ $category->name }}" href="javascript:void(0)" class="editbtn"><button class="btn btn-primary"><i class="fas fa-edit"></i></button></a>
										<a data-id="{{ $category->id }}" data-route="{{ route('categories.destroy',$category->id) }}" href="javascript:void(0)" id="deletebtn"><button class="btn btn-danger"><i class="fas fa-trash"></i></button></a>
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>			
</div>
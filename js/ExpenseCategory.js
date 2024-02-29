$(document).ready(function () {
	var categoryRecords = $('#categoryListing').DataTable({
		"lengthChange": false,
		"processing": true,
		"serverSide": true,
		"bFilter": false,
		'serverMethod': 'post',
		"order": [],
		"ajax": {
			url: "ExpenseCategoryAction.php",
			type: "POST",
			data: { action: 'listCategory' },
			dataType: "json"
		},
		"columnDefs": [
			{
				"targets": [0, 3, 4],
				"orderable": false,
			},
		],
		"pageLength": 10
	});

	$('#addCategory').click(function () {
		$('#categoryModal').modal({
			backdrop: 'static',
			keyboard: false
		});
		$("#categoryModal").on("shown.bs.modal", function () {
			$('#categoryForm')[0].reset();
			$('.modal-title').html("<i class='fa fa-plus'></i> Add Category");
			$('#action').val('addCategory');
			$('#save').val('Save');
		});
	});

	$("#categoryListing").on('click', '.update', function () {
		var id = $(this).attr("id");
		var action = 'getCategoryDetails';
		$.ajax({
			url: 'ExpenseCategoryAction.php',
			method: "POST",
			data: { id: id, action: action },
			dataType: "json",
			success: function (respData) {
				$("#categoryModal").on("shown.bs.modal", function () {
					$('#categoryForm')[0].reset();
					respData.data.forEach(function (item) {
						$('#id').val(item['id']);
						$('#categoryName').val(item['name']);
						$('#status').val(item['status']);
					});
					$('.modal-title').html("<i class='fa fa-plus'></i> Edit category");
					$('#action').val('updateCategory');
					$('#save').val('Save');
				}).modal({
					backdrop: 'static',
					keyboard: false
				});
			}
		});
	});

	$("#categoryModal").on('submit', '#categoryForm', function (event) {
		event.preventDefault();
		$('#save').attr('disabled', 'disabled');
		var formData = $(this).serialize();
		$.ajax({
			url: "ExpenseCategoryAction.php",
			method: "POST",
			data: formData,
			success: function (data) {
				var result = JSON.parse(data);

				if (!result.status) {
					alert(result.message)
					$('#save').removeAttr('disabled');
				} else {
					$('#categoryForm')[0].reset();
					$('#categoryModal').modal('hide');
					$('#save').attr('disabled', false);
					categoryRecords.ajax.reload();
				}
			}
		})
	});

	$("#categoryListing").on('click', '.delete', function () {
		Swal.fire({
			title: "Are you sure?",
			text: "You want to delete this!",
			showCancelButton: true,
			confirmButtonColor: "#3085d6",
			cancelButtonColor: "#d33",
			confirmButtonText: "Yes, delete it!"
		}).then((result) => {
			console.log(result)
			if (result.value) {
				var id = $(this).attr("id");
				var action = "deleteCategory";

				$.ajax({
					url: "ExpenseCategoryAction.php",
					method: "POST",
					data: { id: id, action: action },
					success: function (data) {
						categoryRecords.ajax.reload();
					}
				})
			}
		});

	});
	});
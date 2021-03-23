var counter = $('#annonce_images .row').length;
	deleteblock();
	$('#add_image').click(function(){
		counter++;
		const index = counter;
		var tmpl = $('#annonce_images').data('prototype');
		tmpl = tmpl.replace(/__name__/g, index);
		console.log(tmpl);
		$('#annonce_images').append(tmpl);
		deleteblock();
	})

	function deleteblock() {
		$('.del_image').click(function(){
			$(this).parent().parent().remove();
		})
}
	$('.imageUploaded').click(function (event) {
		// $("#" + event.target.id).remove();
		var value = $('#annonce_idArray').val();
		$(this).remove();
		$('#annonce_idArray').val(event.target.id + "," + value);
	})
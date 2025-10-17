<script>
	function postFormData (url, formData) {
		$.ajax ({
			type: `POST`,
			url: `<?=$PATH;?>Admin/${url}`,
			enctype: `multipart/form-data`,
			dataType: `text`,
			data: formData,
			contentType: false,
			cache: false,
			processData: false,
			beforeSend: () => S_B.html (`<i class="w3-xlarge fa fa-cog w3-spin"></i>`),
			success: resp => {
				$(`i.w3-spin`).remove();
				S_B.append (resp);
<?=($debug??0) ? "":"setTimeout (() => S_B.html (``), 5000);";?>
			},
			error: () => S_B.html (`<p class="w3-yellow"> Server error, please try again. </p>`)
		});
	}
</script>


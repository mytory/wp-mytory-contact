<div class="wrap">
	<h1>연락처 엑셀로 등록</h1>
	<p>예제 엑셀을 다운로드해 이름과 연락처를 채운 뒤 업로드하세요.</p>
	<p><a href="<?php echo $example_excel_href; ?>">👉 예제 다운로드</a></p>
	<form method="post" enctype="multipart/form-data">
		<input required type="file" name="excel" id="excel">
		<?php submit_button('입력'); ?>
	</form>
</div>
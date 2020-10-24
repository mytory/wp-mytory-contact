<?php
/**
 * @var WP_Query $wp_query
 * @var int $paged
 */
?>
<div class="wrap">
	<h1 class="wp-heading-inline">
        연락처 목록
    </h1>

	<?php if (!empty($message)){ ?>
		<div id="message" class="notice notice-info is-dismissible">
			<p><?php echo $message; ?></p>
			<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>
	<?php } ?>

	<form method="post" class="card" style="margin-bottom: 1rem">
		<h2>입력하기</h2>
		<table class="form-table">
			<tr>
				<th><label for="name">이름</label></th>
				<td>
					<input type="text" name="name">
				</td>
			</tr>
			<tr>
				<th><label for="name">전화번호</label></th>
				<td>
					<input type="tel" name="phone">
				</td>
			</tr>
		</table>
		<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="저장"></p>
	</form>

	<?php
	if ( $wp_query->post_count == 0 ) { ?>
        <p>연락처가 없습니다.</p>
	<?php } else { ?>
        <table class="wp-list-table widefat fixed striped table-view-list" style="max-width: 520px;">
            <thead>
            <tr>
                <th>이름</th>
                <th>연락처</th>
            </tr>
            </thead>
            <tbody>
			<?php while ( $wp_query->have_posts() ): $wp_query->the_post(); ?>
                <tr>
                    <td><?php the_title() ?></td>
                    <td><?php echo get_post_meta(get_the_ID(), 'phone', true) ?></td>
                </tr>
			<?php endwhile; ?>
            </tbody>
        </table>
        <div>
			<?php if ( $paged > 1 ) { ?>
                <a href="?paged=<?php echo $paged - 1 ?>">이전</a>
			<?php } ?>
			<?php if ( $paged < $wp_query->max_num_pages ) { ?>
                <a href="?paged=<?php echo $paged - 1 ?>">다음</a>
			<?php } ?>
        </div>
	<?php } ?>
</div>
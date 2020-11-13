<?php
/**
 * @var WP_Query $wp_query
 * @var int $paged
 */
?>
<div class="wrap  js-contact-list">
    <h1 class="wp-heading-inline">
        연락처 목록
    </h1>

	<?php if ( ! empty( $message ) ) { ?>
        <div id="message" class="notice notice-info is-dismissible">
            <p><?php echo $message; ?></p>
            <button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span>
            </button>
        </div>
	<?php } ?>

    <form method="post" class="card" style="margin-bottom: 1rem">
        <h2>입력하기</h2>
        <table class="form-table">
            <tr>
                <th><label for="name">이름</label></th>
                <td>
                    <input required type="text" name="name" id="name">
                </td>
            </tr>
            <tr>
                <th><label for="name">전화번호</label></th>
                <td>
                    <input required type="tel" name="phone" pattern="[0-9]{10,}" title="숫자만 10자리 이상 입력해 주세요"
                           placeholder="예) 01012341234">
                </td>
            </tr>
        </table>
        <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="저장"></p>
    </form>

    <div style="max-width: 520px; margin-bottom: .5em;">
        <form style="float: right;">
            <input type="hidden" name="page" value="mytory_contact">
            <input type="search" name="q" title="검색" placeholder="이름, 전화번호"
                   value="<?php echo esc_attr( $_GET['q'] ?? '' ); ?>">
            <input type="submit" class="button button-primary" value="검색">
        </form>
        <div style="clear: both;"></div>
    </div>

	<?php
	if ( $wp_query->post_count == 0 ) { ?>
        <p>연락처가 없습니다.</p>
	<?php } else { ?>
        <table class="wp-list-table widefat fixed striped table-view-list" style="max-width: 520px;">
            <colgroup>
                <col>
                <col>
                <col style="width: 70px;">
            </colgroup>
            <thead>
            <tr>
                <th>이름</th>
                <th>연락처</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="contact in contactList" v-cloak>
                <td>{{ contact.name }}</td>
                <td>{{ contact.phone }}</td>
                <td>
                    <button class="button  button-small" @click="remove(contact.ID)">삭제</button>
                </td>
            </tr>
            </tbody>
        </table>
        <div style="margin-top: 1em;">
			<?php if ( $paged > 1 ) {
			    parse_str($_SERVER['QUERY_STRING'], $parsed);
				$parsed['paged'] = $paged - 1;
			    ?>
                <a href="?<?php echo http_build_query($parsed) ?>">이전</a>
			<?php } ?>

            <span style="margin: 0 1em;">
                <?php echo (int) $paged ?>페이지
            </span>

			<?php if ( $paged < $wp_query->max_num_pages ) {
				parse_str($_SERVER['QUERY_STRING'], $parsed);
				$parsed['paged'] = $paged + 1;
				?>
                <a href="?<?php echo http_build_query($parsed) ?>">다음</a>
			<?php } ?>
        </div>
	<?php } ?>
</div>

<?php
$contact_list = [];
foreach ( $wp_query->posts as $contact ) {
	$contact_list[] = [
		'ID'    => $contact->ID,
		'name'  => $contact->post_title,
		'phone' => get_post_meta( $contact->ID, 'phone', true ),
	];
}
?>

<script>
    var mytoryContact = {
        contactList: <?php echo json_encode( $contact_list ); ?>
    };
    setTimeout(function () {
        document.getElementById('name').focus();
    }, 500);
</script>
<?php
/**
 * @var int $result_count
 * @var array $already_list
 * @var array $error_list
 */
?>
<div class="wrap">
    <h1>엑셀 등록 결과</h1>

    <p>연락처 <?php echo number_format( $result_count ); ?>개를 입력했습니다.</p>
	<?php if ( count( $already_list ) ) { ?>
        <p>이미 등록돼 있어서 입력하지 않은 연락처는 <?php echo number_format( count( $already_list ) ); ?>개입니다.</p>
	<?php } ?>
	<?php if ( count( $error_list ) ) { ?>
        <p>입력중 에러가 발생했습니다.</p>
        <ul>
			<?php foreach ( $error_list as $message ) { ?>
                <li><?php echo $message; ?></li>
			<?php } ?>
        </ul>
	<?php } ?>
</div>
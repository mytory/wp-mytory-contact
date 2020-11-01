<?php

namespace Mytory\Contact;

class MytoryContact {

	private $has_group = false;

	public function __construct( $args = [] ) {
		$this->has_group = $args['has_group'] ?? false;

		add_action( 'init', [ $this, 'registerPostType' ] );
		if ( $this->has_group ) {
			add_action( 'init', [ $this, 'registerTaxonomy' ] );
		}

		add_action( 'admin_menu', [ $this, 'registerMenus' ] );
	}

	public function registerPostType() {
		$labels = array(
			'name'               => '연락처',
			'singular_name'      => '연락처',
			'add_new'            => '연락처 추가',
			'add_new_item'       => '연락처 추가',
			'edit_item'          => '연락처 수정',
			'new_item'           => '연락처 추가',
			'all_items'          => '연락처 나눔',
			'view_item'          => '연락처 상세 보기',
			'search_items'       => '연락처 검색',
			'not_found'          => '등록된 연락처가 없습니다',
			'not_found_in_trash' => '휴지통에 연락처가 없습니다',
			'parent_item_colon'  => '부모 연락처:',
			'menu_name'          => '연락처 나눔',
		);

		$args = array(
			'labels'      => $labels,
			'public'      => false,
			'has_archive' => false,
			'rewrite'     => [
				'slug' => 'photo',
			],
		);

		register_post_type( 'mytory_contact', $args );
	}


	public function registerTaxonomy() {
		$labels = array(
			'name'                       => '연락처 그룹',
			'singular_name'              => '연락처 그룹',
			'search_items'               => '연락처 그룹 검색',
			'popular_items'              => '많이 쓴 연락처 그룹',
			'all_items'                  => '연락처 그룹 목록',
			'edit_item'                  => '연락처 그룹 수정',
			'view_item'                  => '연락처 그룹 보기',
			'update_item'                => '저장',
			'add_new_item'               => '연락처 그룹 추가',
			'new_item_name'              => '새 연락처 그룹 이름',
			'separate_items_with_commas' => '여러 명 입력하려면 쉽표(,)로 구분하세요',
			'add_or_remove_items'        => '연락처 그룹 추가 혹은 삭제',
			'choose_from_most_used'      => '많이 쓴 연락처 그룹 중 선택',
			'not_found'                  => '연락처 그룹이 없습니다',
			'menu_name'                  => '연락처 그룹',
		);

		$args = array(
			'labels'            => $labels,
			'hierarchical'      => true,
			'show_admin_column' => false,
		);

		register_taxonomy( 'mytory_contact_group', 'mytory_contact', $args );
	}

	public function registerMenus() {
		add_menu_page(
			'연락처',
			'연락처 목록',
			'edit_others_posts',
			'mytory_contact',
			[ $this, 'contactList' ],
			'dashicons-index-card',
			35
		);
	}

	public function contactList() {

		$paged = $_GET['paged'] ?? 1;

		date_default_timezone_set( 'Asia/Seoul' );
		if ( ! empty( $_POST['name'] ) ) {
			$result  = $this->registerContact( $_POST['name'], $_POST['phone'] );
			$message = $result['message'];
		}

		$wp_query = new \WP_Query( [
			'post_type' => 'mytory_contact',
			'paged'     => $paged,
		] );

		include __DIR__ . '/templates/contact-list.php';
	}

	public function registerContact( $name, $phone ) {
		$result = wp_insert_post( [
			'post_type'   => 'mytory_contact',
			'post_title'  => $name,
			'post_status' => 'private',
		], true );

		$response = [
			'result'  => 'success',
			'message' => '',
		];

		if ( is_wp_error( $result ) ) {
			$wp_error            = $result;
			$response['result']  = 'error';
			$response['message'] = implode( "<br>", $wp_error->get_error_messages() );
		} else {
			$ID = $result;
			update_post_meta( $ID, 'phone', $phone );
			$response['message'] = $name . ' 님(' . $phone . ')을 저장했습니다.';
		}

		return $response;
	}
}

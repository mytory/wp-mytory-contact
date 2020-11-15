<?php

namespace Mytory\Contact;

use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use WP_Query;

class MytoryContact {

	public $has_group = false;

	public function __construct( $args = [] ) {

		$this->has_group = $args['has_group'] ?? false;

		add_action( 'init', [ $this, 'registerPostType' ] );
		add_action( 'admin_menu', [ $this, 'registerMenus' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'scripts' ] );
		add_action( 'wp_ajax_mytory_contact_remove', [ $this, 'remove' ] );
		add_action( 'wp_ajax_mytory_contact_save', [ $this, 'save' ] );

		if ( $this->has_group ) {
			add_action( 'init', [ $this, 'registerTaxonomy' ] );
			add_action( 'admin_menu', [ $this, 'registerGroupMenus' ] );
			add_action( 'wp_ajax_mytory_contact_save_group', [ $this, 'saveGroup' ] );
			add_action( 'wp_ajax_mytory_contact_search', [ $this, 'ajaxSearch' ] );
			add_action( 'wp_ajax_mytory_contact_get_group_contact_list', [ $this, 'getGroupContactList' ] );
			add_action( 'wp_ajax_mytory_contact_save_group_contact_list', [ $this, 'saveGroupContactList' ] );
		}
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
			'연락처',
			'edit_others_posts',
			'mytory_contact',
			[ $this, 'contactList' ],
			'dashicons-index-card',
			35
		);

		add_submenu_page(
			'mytory_contact',
			'엑셀로 등록',
			'엑셀로 등록',
			'edit_others_posts',
			'mytory_contact_excel',
			[ $this, 'excel' ]
		);
	}

	public function registerGroupMenus() {
		add_submenu_page(
			'mytory_contact',
			'연락처 그룹 목록',
			'그룹',
			'edit_others_posts',
			'mytory_contact_group_list',
			[ $this, 'groupList' ],
			1
		);
	}

	public function contactList() {

		$paged = $_GET['paged'] ?? 1;

		date_default_timezone_set( 'Asia/Seoul' );
		if ( ! empty( $_POST['name'] ) and ! empty( $_POST['phone'] ) ) {
			$result  = $this->registerContact( $_POST['name'], $_POST['phone'] );
			$message = $result['message'];
		}

		if ( empty( $_GET['q'] ) ) {
			$wp_query = new \WP_Query( [
				'post_type' => 'mytory_contact',
				'paged'     => $paged,
			] );
		} else {
			if ( is_numeric( $_GET['q'] ) ) {
				$wp_query = new \WP_Query( [
					'post_type'  => 'mytory_contact',
					'paged'      => $paged,
					'meta_query' => array(
						array(
							'key'     => 'phone',
							'value'   => $_GET['q'],
							'compare' => 'LIKE'
						),
					),
				] );
			} else {
				$wp_query = new \WP_Query( [
					'post_type' => 'mytory_contact',
					'paged'     => $paged,
					's'         => $_GET['q'],
				] );
			}
		}


		$contact_list = $this->wpQueryToContactList( $wp_query );

		include __DIR__ . '/templates/contact-list.php';
	}

	public function registerContact( $name, $phone ) {
		$response = [
			'result'  => 'success',
			'message' => '',
		];

		$phone = $this->regularizePhone( $phone );

		if ( empty( $name ) ) {
			$response['result']  = 'error';
			$response['message'] = '이름을 입력해 주세요.';

			return $response;
		}

		if ( empty( $phone ) ) {
			$response['result']  = 'error';
			$response['message'] = '전화번호를 입력해 주세요.';

			return $response;
		}

		$wp_query = new WP_Query(
			array(
				'post_type'  => 'mytory_contact',
				'meta_query' => array(
					array(
						'key'   => 'phone',
						'value' => $phone,
					),
				),
			)
		);

		if ( count( $wp_query->posts ) ) {
			$response['result']  = 'success';
			$response['message'] = '이미 등록한 연락처입니다.';
			$response['ID']      = $wp_query->posts[0]->ID;

			return $response;
		}

		$result = wp_insert_post( [
			'post_type'   => 'mytory_contact',
			'post_title'  => $name,
			'post_status' => 'private',
		], true );

		if ( is_wp_error( $result ) ) {
			$wp_error            = $result;
			$response['result']  = 'error';
			$response['message'] = implode( "<br>", $wp_error->get_error_messages() );
		} else {
			$ID = $result;
			update_post_meta( $ID, 'phone', $phone );
			$response['message'] = $name . ' 님(' . $phone . ')을 저장했습니다.';
			$response['ID']      = $ID;
		}

		return $response;
	}

	/**
	 * @param $phone
	 *
	 * @return array
	 */
	private function regularizePhone( $phone ) {
		$phone = preg_replace( '/[^0-9+]/', '', $phone );
		if ( substr( $phone, 0, 3 ) == '+82' ) {
			$phone = preg_replace( '/^\+82/', '0', $phone );
		}

		return $phone;
	}

	/**
	 * @param WP_Query $wp_query
	 *
	 * @return array
	 */
	private function wpQueryToContactList( WP_Query $wp_query ) {
		$contact_list = [];
		foreach ( $wp_query->posts as $contact ) {
			$contact_list[] = [
				'ID'    => $contact->ID,
				'name'  => $contact->post_title,
				'phone' => get_post_meta( $contact->ID, 'phone', true ),
			];
		}

		return $contact_list;
	}

	public function groupList() {
		$term_query = new \WP_Term_Query( [
			'taxonomy'   => 'mytory_contact_group',
			'hide_empty' => false,
			'count'      => true
		] );
		$group_list = $term_query->terms;

		$wp_query      = new WP_Query( [
			'post_type'   => 'mytory_contact',
			'post_status' => 'any',
		] );
		$contact_list  = $this->wpQueryToContactList( $wp_query );
		$max_num_pages = $wp_query->max_num_pages;
		include __DIR__ . '/templates/group-list.php';
	}

	public function excel() {
		if ( empty( $_POST ) ) {
			$mytory_contact_url = theme_url( str_replace( get_template_directory(), '', realpath( __DIR__ . '/..' ) ) );
			$example_excel_href = $mytory_contact_url . '/src/examples/contacts.xlsx';
			if ( $this->has_group ) {
				$example_excel_href = $mytory_contact_url . '/src/examples/group-contacts.xlsx';
			}
			include __DIR__ . '/templates/excel.php';
		} else {

			try {
				$contacts                = $this->readContactsFromExcel( $_FILES['excel']['tmp_name'] );
				$phone_linked_with_comma = implode( ', ', array_map( function ( $phone ) {
					return "'{$phone}'";
				}, array_column( $contacts, 'phone' ) ) );

				/**
				 * @var \wpdb $wpdb
				 */
				global $wpdb;
				$results      = $wpdb->get_results( "select * from {$wpdb->prefix}postmeta where meta_key = 'phone' and meta_value in ({$phone_linked_with_comma})", 'ARRAY_A' );
				$already_list = [];
				if ( count( $results ) ) {
					// 이미 등록돼 있는 연락처는 등록하지 않는다.
					$already_list = array_column( $results, 'meta_value' );
					$contacts     = array_filter( $contacts, function ( $contact ) use ( $already_list ) {
						return ! in_array( $contact['phone'], $already_list );
					} );
				}


				$error_list   = [];
				$result_count = 0;
				if ( $this->has_group ) {
					$this->batchInsertGroup( $contacts );
					$group_map = $this->getGroupMap( $contacts );
				}
				foreach ( $contacts as $contact ) {
					$result = wp_insert_post( [
						'post_type'   => 'mytory_contact',
						'post_title'  => $contact['name'],
						'post_status' => 'private',
					], true );

					if ( is_wp_error( $result ) ) {
						$wp_error     = $result;
						$error_list[] = $wp_error->get_error_messages();
					} else {
						$ID = $result;
						update_post_meta( $ID, 'phone', $contact['phone'] );
						$result_count ++;
					}
					if ( $this->has_group and ! empty( $contact['group'] ) ) {
						wp_add_object_terms( $ID, $group_map[ $contact['group'] ]->term_id, 'mytory_contact_group' );
					}
				}

				include __DIR__ . '/templates/excel-result.php';
			} catch ( Exception $e ) {
				echo $e->getMessage();
				die();
			}
		}
	}

	/**
	 * @param $excel_path
	 *
	 * @return array
	 * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
	 */
	private function readContactsFromExcel( $excel_path ): array {
		$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile( $excel_path );
		$reader->setReadDataOnly( true );
		$spreadsheet = $reader->load( $_FILES['excel']['tmp_name'] );
		$worksheet   = $spreadsheet->getActiveSheet();

		$contacts = [];

		foreach ( $worksheet->getRowIterator() as $row ) {
			if ( $row->getRowIndex() === 1 ) {
				// 1번줄은 제목줄이므로 통과
				continue;
			}
			$contact      = [];
			$cellIterator = $row->getCellIterator();
			$cellIterator->setIterateOnlyExistingCells( false );
			foreach ( $cellIterator as $cell ) {
				/**
				 * @var Cell $cell
				 */
				if ( $cell->getColumn() === 'A' ) {
					$contact['name'] = $cell->getValue();
				}
				if ( $cell->getColumn() === 'B' ) {
					$contact['phone'] = $this->regularizePhone( $cell->getValue() );
				}
				if ( $cell->getColumn() === 'C' ) {
					$contact['group'] = $cell->getValue();
				}
			}
			$contacts[] = $contact;
		}

		$contacts = array_filter( $contacts, function ( $contact ) {
			return ! empty( $contact['name'] ) and ! empty( $contact['phone'] );
		} );

		return $contacts;
	}

	private function batchInsertGroup( array $contacts ) {
		$group_names = array_filter( array_unique( array_column( $contacts, 'group' ) ) );
		foreach ( $group_names as $group_name ) {
			if ( ! get_term_by( 'name', $group_name, 'mytory_contact_group' ) ) {
				$result = wp_insert_term( $group_name, 'mytory_contact_group' );
				if ( is_wp_error( $result ) ) {
					$wp_error = $result;
				}
			}
		}
	}

	private function getGroupMap( array $contacts ) {
		// 중복 제거, 빈갑 제거.
		$group_names = array_unique( array_column( array_filter( $contacts, function ( $contact ) {
			return ! empty( $contact['group'] );
		} ), 'group' ) );

		$group_map = [];
		foreach ( $group_names as $group_name ) {
			$group = get_term_by( 'name', $group_name, 'mytory_contact_group' );
			if ( $group ) {
				$group_map[ $group_name ] = $group;
			}
		}

		return $group_map;
	}

	function scripts() {
		$mytory_contact_url = theme_url( str_replace( get_template_directory(), '', realpath( __DIR__ . '/..' ) ) );
		$version            = filemtime( realpath( __DIR__ . '/../dist/mytory-contact.js' ) );
		wp_enqueue_script( 'mytory-contact', $mytory_contact_url . '/dist/mytory-contact.js', [], $version, true );

		$version = filemtime( realpath( __DIR__ . '/../src/mytory-contact.css' ) );
		wp_enqueue_style( 'mytory-contact', $mytory_contact_url . '/src/css/mytory-contact.css', [], $version, 'all' );
	}

	function remove() {
		if ( wp_delete_post( $_POST['id'] ) ) {
			$res = [
				'result' => 'success',
			];
		} else {
			$res = [
				'result' => 'error',
			];
		}
		echo json_encode( $res );
		die();
	}

	public function save() {
		$contact = $_POST['contact'];
		$result  = wp_update_post( [
			'ID'         => $contact['ID'],
			'post_title' => $contact['name'],
		], true );
		if ( is_wp_error( $result ) ) {
			$wp_error = $result;
			echo json_encode( [
				'result'  => 'error',
				'message' => $wp_error->get_error_messages(),
			] );
		} else {
			$ID    = $result;
			$phone = $this->regularizePhone( $contact['phone'] );
			update_post_meta( $ID, 'phone', $phone );
			echo json_encode( [
				'result'  => 'success',
				'message' => '저장했습니다.',
				'contact' => [
					'ID'    => $ID,
					'name'  => $contact['name'],
					'phone' => $phone,
				]
			] );
		}
		die();
	}

	public function saveGroup() {

		$term = get_term_by( 'name', $_POST['name'], 'mytory_contact_group' );

		if ( $term ) {
			echo json_encode( [
				'result'  => 'info',
				'message' => '같은 이름의 그룹이 이미 있습니다. 다른 이름을 사용하세요 😀',
			] );
			die();
		}

		if ( empty( $_POST['name'] ) ) {
			echo json_encode( [
				'result'  => 'info',
				'message' => '그룹 이름을 입력해 주세요.',
			] );
			die();
		}

		if ( ! empty( $_POST['name'] ) ) {

			$result = wp_insert_term( $_POST['name'], 'mytory_contact_group' );

			if ( is_wp_error( $result ) ) {
				$wp_error = $result;
				echo json_encode( [
					'result'  => 'error',
					'message' => implode( "\n", $wp_error->get_error_messages() ),
				] );
				die();
			}

			echo json_encode( [
				'result'  => 'success',
				'message' => '저장했습니다.',
				'group'   => get_term( $result['term_id'] ),
			] );
		}

		die();
	}

	public function ajaxSearch() {
		if ( is_numeric( $_POST['q'] ) ) {
			$wp_query = new \WP_Query( [
				'post_type'  => 'mytory_contact',
				'paged'      => $_POST['paged'],
				'meta_query' => array(
					array(
						'key'     => 'phone',
						'value'   => $_POST['q'],
						'compare' => 'LIKE'
					),
				),
			] );
		} else {
			$wp_query = new \WP_Query( [
				'post_type' => 'mytory_contact',
				'paged'     => $_POST['paged'],
				's'         => $_POST['q'],
			] );
		}

		echo json_encode( [
			'result'        => 'success',
			'contact_list'  => $this->wpQueryToContactList( $wp_query ),
			'max_num_pages' => $wp_query->max_num_pages,
		] );
		die();
	}

	public function getGroupContactList( $term_id = null ) {
		$term_id  = $term_id ?: (int) $_POST['term_id'];
		$wp_query = new WP_Query( [
			'post_type'      => 'mytory_contact',
			'post_status'    => 'any',
			'posts_per_page' => - 1,
			'tax_query'      => array(
				array(
					'taxonomy' => 'mytory_contact_group',
					'field'    => 'term_id',
					'terms'    => $term_id,
				)
			)
		] );
		echo json_encode( [
			'result'       => 'success',
			'contact_list' => $this->wpQueryToContactList( $wp_query ),
		] );
		die();
	}

	public function saveGroupContactList() {
		$group        = $_POST['group'];
		$contact_list = $_POST['group_contact_list'];
		foreach ( $contact_list as $item ) {
			wp_add_object_terms( (int) $item['ID'], [ (int) $group['term_id'] ], 'mytory_contact_group' );
		}

		echo json_encode( [
			'result'  => 'success',
			'message' => '저장했습니다.',
		] );

		die();
	}
}

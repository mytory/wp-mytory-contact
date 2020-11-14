<?php
/**
 * @var WP_Term[] $group_list
 * @var array $contact_list
 * @var int $max_num_pages
 */
?>
<div class="wrap  js-group-list">
    <h1>연락처 그룹 목록</h1>

    <form class="card" @submit.prevent="save" style="margin-bottom: 1rem">
        <table class="form-table">
            <tr>
                <th><label for="name">그룹 이름</label></th>
                <td>
                    <input required type="text" name="name" id="name">
                </td>
            </tr>
        </table>
        <p class="submit">
            <input type="submit" name="submit" id="submit" class="button button-primary" value="새 그룹 만들기">
        </p>
    </form>

    <p v-if="groupList.length === 0">연락처 그룹이 없습니다. 그룹을 만들면 그룹별로 카카오톡 메시지를 따로 전송할 수 있습니다.</p>

    <div class="mcg-card">
        <button class="mcg-card__item" v-for="group in groupList" v-cloak
                @click="showGroupManager = true; selectedGroup = group;">
            <div class="mcg-card__tag">{{ group.count }}개</div>
            <div class="mcg-card__name">{{ group.name }}</div>
        </button>
    </div>

    <group-manager v-if="showGroupManager"
                   @close="showGroupManager = false"
                   @save="saveGroupContacts"
                   :group="selectedGroup"
                   :contact-list="contactList"
                   :max-num-pages="maxNumPages"></group-manager>
</div>

<script>
    var mytoryContact = {
        groupList: <?php echo json_encode( $group_list ); ?>,
        contactList: <?php echo json_encode( $contact_list ); ?>,
        maxNumPages: <?php echo json_encode( $max_num_pages ); ?>
    };
</script>
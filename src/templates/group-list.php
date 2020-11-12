<?php
/**
 * @var WP_Term[] $group_list
 */
?>
<div class="wrap  js-group-list">
    <h1>연락처 그룹 목록</h1>

    <form class="card" @submit.prevent="save" style="margin-bottom: 1rem">
        <h2>입력하기</h2>
        <table class="form-table">
            <tr>
                <th><label for="name">그룹 이름</label></th>
                <td>
                    <input required type="text" name="name" id="name">
                </td>
            </tr>
        </table>
        <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="저장"></p>
    </form>

    <div class="mcg-card">
        <button class="mcg-card__item" v-for="group in groupList"
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
                   :group-contact-list="selectedGroupContactList"></group-manager>
</div>

<script>
    var mytoryContact = {
        groupList: <?php echo json_encode( $group_list ); ?>,
        contactList: <?php echo json_encode( $contact_list ); ?>
    };
</script>
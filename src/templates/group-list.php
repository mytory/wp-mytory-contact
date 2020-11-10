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

    <ul>
        <li v-for="group in groupList">
            {{ group.name }}({{ group.count }}개)
        </li>
    </ul>
</div>

<script>
    var mytoryContact = {
        groupList: <?php echo json_encode( $group_list ); ?>
    };
</script>
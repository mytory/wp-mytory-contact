<?php
/**
 * @var WP_Term[] $group_list
 */
?>
<style>
    .mcg-card {
        margin: 0;
        padding: 0;
        display: grid;
        grid-template-columns: 1fr 1fr;
        grid-gap: 1em;
    }
    .mcg-card__item {
        list-style: none;
        background-color: #fff;
        box-shadow: 0 0 1px 1px rgba(0,0,0,0.2);
        padding: 3em 1em;
        margin: 0;
        text-align: center;
        position: relative;
        color: black;
        text-decoration: none;
    }
    .mcg-card__item:hover {
        box-shadow: 0 0 3px 1px rgba(0,0,0,0.4);
    }
    .mcg-card__name {
        font-size: 1.1rem;
        font-weight: bold;
    }
    .mcg-card__tag {
        position: absolute;
        right: 1em;
        top: 1em;
        color: #aaa;
    }
    @media screen and (min-width: 600px) {
        .mcg-card {
            grid-template-columns: 1fr 1fr 1fr;
        }
    }
    @media screen and (min-width: 980px) {
        .mcg-card {
            grid-template-columns: 1fr 1fr 1fr 1fr;
        }
    }
    @media screen and (min-width: 1280px) {
        .mcg-card {
            grid-template-columns: 1fr 1fr 1fr 1fr 1fr 1fr;
        }
    }
</style>
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
        <a href="#" class="mcg-card__item" v-for="group in groupList">
            <div class="mcg-card__tag">{{ group.count }}개</div>
            <div class="mcg-card__name">{{ group.name }}</div>
        </a>
    </div>
</div>

<script>
    var mytoryContact = {
        groupList: <?php echo json_encode( $group_list ); ?>
    };
</script>
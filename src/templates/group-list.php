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
        box-shadow: 0 0 1px 1px rgba(0, 0, 0, 0.2);
        padding: 3em 1em;
        margin: 0;
        text-align: center;
        position: relative;
        color: black;
        text-decoration: none;
    }

    .mcg-card__item:hover {
        box-shadow: 0 0 3px 1px rgba(0, 0, 0, 0.4);
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

    .modal-mask {
        position: fixed;
        z-index: 9998;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, .5);
        display: table;
        transition: opacity .3s ease;
    }

    .modal-wrapper {
        display: table-cell;
        vertical-align: middle;
    }

    .modal-container {
        position: relative;
        width: 90vw;
        max-width: 50rem;
        max-height: 90vh;
        overflow: auto;
        margin: 0 auto;
        background-color: #fff;
        border-top: 2px solid $ color-brand-primary;
        border-radius: 2px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, .33);
        transition: all .3s ease;
        scroll-behavior: smooth;
        -webkit-overflow-scrolling: touch;
    }

    .modal-header {
        left: 5%;
        width: 90%;
        padding: 1em 2em;
        position: fixed;
        background-color: #eee;
        color: white;
        border-radius: 2px 2px 0 0;
    }

    .modal-body {
        padding: 2em;
    }

    .modal-body img {
        display: block;
        max-width: 100%;
        height: auto;
    }

    @media screen and (max-width: 600px) {
        .modal-body {
            padding: 1em;
        }
    }

    .modal-default-button {
        float: right;
    }

    /*
	 * The following styles are auto-applied to elements with
	 * transition="modal" when their visibility is toggled
	 * by Vue.js.
	 *
	 * You can easily play with the modal transition by editing
	 * these styles.
	 */

    .modal-enter {
        opacity: 0;
    }

    .modal-leave-active {
        opacity: 0;
    }

    .modal-enter .modal-container,
    .modal-leave-active .modal-container {
        -webkit-transform: scale(1.1);
        transform: scale(1.1);
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
        <a class="mcg-card__item" v-for="group in groupList" @click="showGroupManager = true; selectedGroup = group;">
            <div class="mcg-card__tag">{{ group.count }}개</div>
            <div class="mcg-card__name">{{ group.name }}</div>
        </a>
    </div>

    <group-manager v-if="showGroupManager"
                   @close="showGroupManager = false"
                   @save="saveGroupContacts"
                   :group="selectedGroup"></group-manager>
</div>

<script>
    var mytoryContact = {
        groupList: <?php echo json_encode( $group_list ); ?>
    };
</script>
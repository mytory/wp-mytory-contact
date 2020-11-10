import Vue from 'vue';
import axios from 'axios';
import qs from 'qs';
import swal from 'sweetalert';

new Vue({
    el: '.js-group-list',
    data: {
        groupList: mytoryContact.groupList || []
    },
    methods: {
        save() {
            const name = document.getElementById('name').value.trim();
            if (!name) {
                swal('이름을 입력해 주세요.');
                return;
            }
            axios({
                method: 'POST',
                url: ajaxurl,
                headers: {'content-type': 'application/x-www-form-urlencoded'},
                data: qs.stringify({name: name, action: 'mytory_contact_save_group'})
            }).then(res => {
                if (res.data.result === 'success') {
                    this.groupList.push(res.data.group);
                    swal(`${res.data.group.name} 그룹을 저장했습니다.`)
                        .then(() => {
                            document.getElementById('name').value = '';
                            document.getElementById('name').focus();
                        });
                    this.groupList = this.groupList.sort((a, b) => (a.name > b.name) ? 1 : -1);
                } else {
                    throw '관리자에게 문의하세요.';
                }
            }).catch(error => {
                swal({
                    title: '에러 발생!',
                    text: error,
                    icon: 'error'
                });
            });
        }
    },
    created() {
        setTimeout(function () {
            document.getElementById('name').focus();
        }, 500);
    }
});
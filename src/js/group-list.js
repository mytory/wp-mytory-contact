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
                    swal({
                        title: `${res.data.group.name} 그룹을 저장했습니다.`,
                        icon: 'success'
                    })
                        .then(() => {
                            document.getElementById('name').value = '';
                            document.getElementById('name').focus();
                        });
                    this.groupList = this.groupList.sort((a, b) => (a.name > b.name) ? 1 : -1);
                } else {
                    throw res.data;
                }
            }).catch(error => {
                swal({
                    title: '문제가 있습니다',
                    text: error.message,
                    icon: error.result
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
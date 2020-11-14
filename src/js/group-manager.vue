<template>
    <transition name="modal">
        <div class="modal-mask">
            <div class="modal-wrapper">
                <div class="modal-container">
                    <header class="modal-header">
                        <h1 style="float: left; padding: 0;">{{ group.name }}</h1>

                        <div style="float: right;">
                            <button class="button  button-primary" @click="save()" :disabled="isProcessing">
                                저장
                            </button>
                            <button class="button" @click="close" :disabled="isProcessing">
                                취소
                            </button>
                        </div>
                    </header>

                    <div class="modal-body">
                        <div class="o-layout">
                            <div class="o-layout__item  u-6/12">
                                <h2>전체 연락처</h2>
                                <form style="text-align: right; margin-bottom: 1em;" @submit.prevent="search(1)">
                                    <input type="search" name="q" title="검색" placeholder="이름, 전화번호" v-model="q">
                                    <input type="submit" class="button  button-primary" value="검색">
                                </form>

                                <div style="text-align: center; font-size: 0.9rem; margin-bottom: 1em;">
                                    <button class="button-link" v-if="paged > 1"
                                            @click="search(--paged)">이전
                                    </button>
                                    <span style="margin: 0 1em">{{ paged }}페이지</span>
                                    <button class="button-link" v-if="paged < myMaxNumPages"
                                            @click="search(++paged)">다음
                                    </button>
                                </div>

                                <div class="u-text-center" v-show="isProcessing">
                                    <div class="lds-roller"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
                                </div>

                                <table class="hover-table  u-12/12" style="font-size: 0.9rem;" v-show="!isProcessing">
                                    <tr v-for="(contact, index) in myContactList">
                                        <td>{{ contact.name }}</td>
                                        <td style="font-family: monospace;">{{ contact.phone }}</td>
                                        <td>
                                            <button class="button  button-primary  button-small" @click="add(contact)"
                                                    v-show="!isSelected(contact)">
                                                추가
                                            </button>
                                            <button class="button  button-small" @click="remove(contact)"
                                                    v-show="isSelected(contact)">
                                                빼기
                                            </button>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="o-layout__item  u-6/12">
                                <h2>{{ group.name }} 연락처</h2>
                                <p v-if="groupContactList.length === 0">
                                    {{ group.name }} 그룹에 추가할 연락처를 좌측에서 선택해 주세요.
                                </p>
                                <table class="hover-table  u-12/12" style="font-size: 0.9rem;">
                                    <tr v-for="(contact, index) in groupContactList">
                                        <td>{{ contact.name }}</td>
                                        <td style="font-family: monospace;">{{ contact.phone }}</td>
                                        <td>
                                            <button class="button  button-small" @click="remove(contact)">빼기</button>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>

<script>
    import axios from "axios";
    import qs from "qs";

    export default {
        name: "group-manager",
        data() {
            return {
                myContactList: this.contactList,
                myMaxNumPages: this.maxNumPages,
                groupContactList: [],
                isProcessing: false,
                paged: 1,
                q: ''
            }
        },
        methods: {
            add(contact) {
                if (!this.isSelected(contact)) {
                    this.groupContactList.push(contact);
                }
            },
            remove(contact) {
                this.groupContactList = this.groupContactList.filter(item => contact.phone !== item.phone);
            },
            isSelected(contact) {
                return (this.groupContactList.filter(item => item.phone === contact.phone).length > 0);
            },
            search(paged) {
                this.isProcessing = true;
                axios({
                    method: 'POST',
                    url: ajaxurl,
                    headers: {'content-type': 'application/x-www-form-urlencoded'},
                    data: qs.stringify({q: this.q, paged: paged, action: 'mytory_contact_search'})
                }).then(res => {
                    this.isProcessing = false;
                    if (res.data.result === 'success') {
                        this.myContactList = res.data.contact_list;
                        this.myMaxNumPages = res.data.max_num_pages;
                        this.paged = paged;
                    } else {
                        throw res.data;
                    }
                }).catch(error => {
                    swal("문제가 발생했습니다", error.message, error.result);
                });
            },
            save() {
                this.isProcessing = true;
                axios({
                    method: 'POST',
                    url: ajaxurl,
                    headers: {'content-type': 'application/x-www-form-urlencoded'},
                    data: qs.stringify({
                        group: this.group,
                        group_contact_list: this.groupContactList,
                        action: 'mytory_contact_save_group_contact_list'
                    })
                }).then(res => {
                    this.isProcessing = false;
                    if (res.data.result === 'success') {
                        this.$emit('close');
                    } else {
                        throw res.data;
                    }
                }).catch(error => {
                    swal("문제가 발생했습니다", error.message, error.result);
                });
            },
            close() {
                swal('저장하지 않은 데이터가 날아갑니다. 취소하고 닫을까요?', {
                    buttons: {
                        open: '열어 두기',
                        close: {
                            'text': '닫기',
                            'value': 'close',
                            className: 'swal-button swal-button--confirm swal-button--danger'
                        }
                    }
                }).then(data => {
                    switch (data) {
                        case 'close':
                            this.$emit('close');
                            break;
                        case 'open':
                            break;
                    }
                }).catch(error => {
                    swal(error);
                });

            }
        },
        props: ['group', 'contactList', 'maxNumPages'],
        created() {
            this.isProcessing = true;
            axios({
                method: 'POST',
                url: ajaxurl,
                headers: {'content-type': 'application/x-www-form-urlencoded'},
                data: qs.stringify({term_id: this.group.term_id, action: 'mytory_contact_get_group_contact_list'})
            }).then(res => {
                this.isProcessing = false;
                if (res.data.result === 'success') {
                    this.groupContactList = res.data.contact_list;
                } else {
                    throw res.data;
                }
            }).catch(error => {
                swal("문제가 발생했습니다", error.message, error.result);
            });
        }
    }
</script>


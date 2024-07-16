<template>
    <v-data-table 
        :headers="headers"
        :items="users"
        :loading="false"
        hide-actions
        class="elevation-1">
        <template slot="items" slot-scope="props">
            
            <td class="text-xs-left pa-2" @click="select(props.item)" :class="{'blue lighten-3':is_selected(props.item)}">{{ props.item.user_full_name }}</td>
            <td class="text-xs-right pa-2" @click="select(props.item)" :class="{'blue lighten-3':is_selected(props.item)}">{{ one_money(props.item.fee) }}</td>
        </template>
    </v-data-table>
</template>

<style scoped>
    .v-list--two-line .v-list__tile {
        height: 64px;
    }

</style>

<script>
module.exports = {
    components : {
        // 'user-group-edit' : httpVueLoader('./user-group-edit.vue')
    },

    data () {
        return {
            headers: [
                {
                    text: "NAMA ADMIN",
                    align: "left",
                    sortable: false,
                    width: "70%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "FEE TOTAL",
                    align: "right",
                    sortable: false,
                    width: "30%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                }
            ]
        }
    },

    computed : {
        users () {
            return this.$store.state.fee.users
        },

        selected_user () {
            return this.$store.state.fee.selected_user
        }
    },

    methods : {
        one_money (x) {
            return window.one_money(x)
        },

        select (x) {
            this.$store.commit('fee/set_selected_user', x)
            // this.$store.commit('fee/set_privileges', x.privilege)
            this.$store.dispatch('fee/search', {})
        },

        is_selected (x) {
            if (!this.selected_user)
                return false
            if (this.selected_user.user_id == x.user_id)
                return true
            return false
        },

        // edit (x) {
        //     this.select(x)
        //     this.$store.commit('fee/set_common', ['dialog_group', true])
        // },

        // add_user () {
        //     this.$store.commit('user_new/set_common', ['edit', false])
        //     this.$store.commit('user_new/set_common', ['dialog_user', true])

        //     this.$store.commit('user_new/set_common', ['user_name', ''])
        //     this.$store.commit('user_new/set_common', ['user_full_name', ''])
        //     this.$store.commit('user_new/set_common', ['user_address', ''])
        //     this.$store.commit('user_new/set_common', ['user_phone', ''])
        //     this.$store.commit('user_new/set_common', ['user_email', ''])
        // }
    },

    mounted () {
        // this.$store.dispatch('fee/search_users')
        this.$store.dispatch('fee/search_user')
    }
}
</script>
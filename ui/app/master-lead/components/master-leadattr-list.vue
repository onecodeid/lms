<template>
    <v-card>
        <v-card-title primary-title class="pt-2 pb-0">
            <v-layout row wrap>
                <v-flex xs5 pb-2>
                    <h3 class="display-1 font-weight-light zalfa-text-title">MASTERDATA ATRIBUT LEAD</h3>
                </v-flex>
                <v-flex xs2 class="pr-2">
                    <v-select :items="leadAttrCodes" item-value="id" item-text="text" v-model="searchLeadAttrCode"
                                label="Tipe Atribut" solo hide-details dense clearable></v-select>
                </v-flex>
                <v-flex xs2 class="text-xs-right pr-4">
                    <!-- <v-btn color="success" class="ma-0 btn-icon" @click="add">
                        <v-icon>add</v-icon>
                    </v-btn> -->

                    <v-text-field
                        solo
                        hide-details
                        placeholder="Pencarian" v-model="query"
                        @change="search"
                    >
                        <template v-slot:append-outer>
                            <v-btn color="primary" class="ma-0 btn-icon" @click="search">
                                <v-icon>search</v-icon>
                            </v-btn>      

                            <v-btn color="success" class="ma-0 ml-2 btn-icon" @click="add">
                                <v-icon>add</v-icon>
                            </v-btn>  
                        </template>
                    </v-text-field>
                </v-flex>
                <v-flex xs3>
                    <master-tabs></master-tabs>
                </v-flex>
            </v-layout>
        </v-card-title>
        <v-card-text class="pt-0">
            <v-data-table 
                :headers="headers"
                :items="leadAttrs"
                :loading="false"
                hide-actions
                class="elevation-1">
                <template slot="items" slot-scope="props">
                    
                    <td class="text-xs-left pa-2" @click="select(props.item)">{{ codes[props.item.attr_code] }}</td>
                    <td class="text-xs-left pa-2" @click="select(props.item)">{{ props.item.attr_name }}</td>                    
                    
                    <td class="text-xs-center pa-0" @click="select(props.item)">
                        <v-btn color="primary" class="btn-icon ma-0" small @click="edit(props.item)"><v-icon>create</v-icon></v-btn>
                        <v-btn color="red" dark class="btn-icon ma-0" small @click="del(props.item)"><v-icon>delete</v-icon></v-btn>
                    </td>
                </template>
            </v-data-table>
            <v-divider></v-divider>
            <v-pagination
                style="margin-top:10px;margin-bottom:10px"
                v-model="curr_page"
                :length="totalPage"
                @input="changePage"
            ></v-pagination>
        </v-card-text>
        
        <common-dialog-delete :data="0" @confirm_del="confirm_del" v-if="dialog_delete"></common-dialog-delete>
    </v-card>
</template>

<style scoped>
.v-text-field.v-text-field--solo .v-input__control {
    min-height: 36px;
}
.v-text-field.v-text-field--solo .v-input__append-outer {
    margin-top: 0px;
    margin-left: 0px;
}
</style>

<script>
module.exports = {
    components : {
        "common-dialog-delete" : httpVueLoader("../../common/components/common-dialog-delete.vue"),
        "master-tabs" : httpVueLoader("./master-tabs.vue")
    },

    data () {
        return {
            headers: [
                {
                    text: "TIPE ATRIBUT",
                    align: "left",
                    sortable: false,
                    width: "15%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "NAMA / DESKRIPSI ATRIBUT",
                    align: "left",
                    sortable: false,
                    width: "75%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "ACTION",
                    align: "center",
                    sortable: false,
                    width: "10%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                }
            ]
        }
    },

    computed : {
        ...Vuex.mapState('leadAttr', ['leadAttrs', 'leadAttrCodes', 'total', 'totalPage']),
        __s () { return this.$store.state.leadAttr },

        dialog_delete () {
            return this.$store.state.dialog_delete
        },

        leadtype_id () {
            return this.$store.state.leadtype.selected_leadtype.M_LeadTypeID
        },

        query : {
            get () { return this.$store.state.leadAttr.search },
            set (v) { this.__c('search', v) }
        },

        curr_page : {
            get () { return this.$store.state.leadAttr.current_page },
            set (v) { this.$store.commit('leadAttr/setObject', ['current_page', v]) }
        },

        xtotal_page () {
            return this.$store.state.leadtype.total_leadtype_page
        },

        code_prefix () {
            return this.$store.state.leadtype_new.leadtype_prefix
        },

        codes () {
            let cds = {}
            
            for (let c of this.leadAttrCodes) cds[c.id] = c.text
            return cds
        },

        searchLeadAttrCode : {
            get () { return this.__s.searchLeadAttrCode },
            set (v) { this.__c('searchLeadAttrCode', v) }
        }
    },

    methods : {
        __c (a, b) { this.$store.commit('leadAttr/setObject', [a, b]) },

        one_money (x) {
            return window.one_money(x)
        },

        add () {
            this.__c('edit', false)
            this.__c('attrName', '')
            this.__c('selectedLeadAttrCode', null)
            this.__c('dialog', true)
        },

        edit (x) {
            this.select(x)
            let sc = x

            this.__c('edit', true)
            this.__c('attrName', x.attr_name)
            this.__c('selectedLeadAttrCode', x.attr_code)
            this.__c('dialog', true)
        },

        del (x) {
            this.select(x)
            this.$store.commit('set_dialog_delete', true)
        },

        confirm_del (x) {
            this.$store.dispatch('leadAttr/del')
        },

        select (x) {
            this.__c('selectedLeadAttr', x)
        },

        search () {
            return this.$store.dispatch('leadAttr/search')
        },

        changePage(x) {
            this.curr_page = x
            this.search()
        }
    },

    mounted () {
        this.$store.dispatch('leadAttr/search')
    }
}
</script>
<template>
    <v-card>
        <v-card-title primary-title class="pt-2 pb-0">
            <v-layout row wrap>
                <v-flex xs9>
                    <h3 class="display-1 font-weight-light zalfa-text-title">INFORMATION AND ANNOUNCEMENT</h3>
                </v-flex>
                <v-flex xs3 class="text-xs-right">
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
            </v-layout>
        </v-card-title>
        <v-card-text class="pt-2">
            <v-data-table 
                :headers="headers"
                :items="infos"
                :loading="false"
                hide-actions
                class="elevation-1">
                <template slot="items" slot-scope="props">
                    
                    
                    <td class="text-xs-left pa-2" @click="select(props.item)">
                        <!-- <v-badge
                            color="green lighten-2"
                            left
                            overlap
                            v-show="props.item.info_pin=='Y'"
                            >
                            <template v-slot:badge>
                                <v-icon
                                dark
                                small
                                 
                                >
                                bookmark
                                </v-icon>
                            </template>
                            <v-chip :color="props.item.color_class" :text-color="props.item.color_text_class">{{ props.item.info_name }}</v-chip>
                        </v-badge>
                        <v-chip v-show="props.item.info_pin!='Y'" :color="props.item.color_class" :text-color="props.item.color_text_class">{{ props.item.info_name }}</v-chip> -->
                        {{ props.item.info_title }}
                    </td>   
                    <td class="text-xs-left pa-2" @click="select(props.item)">{{ props.item.info_excerpt }}</td>   
                    <td class="text-xs-left pa-2" @click="select(props.item)">{{ props.item.info_sdate }} s/d {{ props.item.info_edate }}</td>
                    <td class="text-xs-left pa-2" @click="select(props.item)">
                        <v-btn color="cyan" :disabled="props.item.info_up=='Y'" :dark="props.item.info_up=='N'" class="btn-icon ma-0" small @click="edit(props.item)">UP</v-btn>
                    </td>                
                    
                    <td class="text-xs-center pa-0" @click="select(props.item)">
                        <!-- <v-btn :color="props.item.info_pin=='Y'?'orange white--text':'success'" 
                            class="btn-icon ma-0" small @click="pin(props.item)" :disabled="props.item.info_special!=''"><v-icon>bookmark</v-icon></v-btn> -->
                        <v-btn color="primary" class="btn-icon ma-0" small @click="edit(props.item)"><v-icon>create</v-icon></v-btn>
                        <v-btn color="primary" class="btn-icon ma-0" small @click="get(props.item)">G</v-btn>
                        <!-- <v-btn color="red" dark class="btn-icon ma-0" small @click="del(props.item)" :disabled="props.item.info_special!=''"><v-icon>delete</v-icon></v-btn> -->
                    </td>
                </template>
            </v-data-table>
            <v-divider></v-divider>
            <v-pagination
                style="margin-top:10px;margin-bottom:10px"
                v-model="curr_page"
                :length="xtotal_page"
                @input="change_page"
            ></v-pagination>
        </v-card-text>
        
        <common-dialog-delete :data="info_id" @confirm_del="confirm_del" v-if="dialog_delete"></common-dialog-delete>
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

.v-badge--overlap.v-badge--left .v-badge__badge {
    left: -4px;
}

.v-badge--overlap .v-badge__badge {
    top: -4px;
}
</style>

<script>
module.exports = {
    components : {
        "common-dialog-delete" : httpVueLoader("../../common/components/common-dialog-delete.vue")
    },

    data () {
        return {
            headers: [
                {
                    text: "NAMA TEMPLATE",
                    align: "left",
                    sortable: false,
                    width: "15%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "KONTENT",
                    align: "left",
                    sortable: false,
                    width: "53%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "TANGGAL BERLAKU",
                    align: "left",
                    sortable: false,
                    width: "15%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "UP",
                    align: "left",
                    sortable: false,
                    width: "7%",
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
        infos () {
            return this.$store.state.info.infos
        },

        dialog_delete () {
            return this.$store.state.dialog_delete
        },

        info_id () {
            return this.$store.state.info.selected_info.info_id
        },

        query : {
            get () { return this.$store.state.info.search },
            set (v) { this.$store.commit('info/update_search', v) }
        },

        curr_page : {
            get () { return this.$store.state.info.current_page },
            set (v) { this.$store.commit('info/update_current_page', v) }
        },

        xtotal_page () {
            return this.$store.state.info.total_info_page
        }
    },

    methods : {
        one_money (x) {
            return window.one_money(x)
        },

        add () {
            this.$store.commit('info_new/set_common', ['edit', false])
            this.$store.commit('info_new/set_common', ['info_name', ''])
            this.$store.commit('info_new/set_object', ['selected_color', null])
            this.$store.commit('info_new/set_content', '')
            this.$store.commit('info_new/set_common', ['dialog_new', true])
        },

        edit (x) {
            this.select(x)
            let sc = x
            this.$store.commit('info_new/set_common', ['edit', true])
            this.$store.commit('info_new/set_common', ['info_title', sc.info_title])
            this.$store.commit('info_new/set_common', ['info_img', sc.img_uri])
            this.$store.commit('info_new/set_common', ['info_sdate', sc.info_sdate])
            this.$store.commit('info_new/set_common', ['info_edate', sc.info_edate])
            this.$store.commit('info_new/set_content', sc.info_content)

            // this.$store.commit('info_new/set_object', ['selected_color', null])
            // for (let color of this.$store.state.info_new.colors) {
            //     if (color.color_id == x.color_id)
            //         this.$store.commit('info_new/set_object', ['selected_color', color])
            // }

            this.$store.commit('info_new/set_common', ['dialog_new', true])
        },

        del (x) {
            this.select(x)
            this.$store.commit('set_dialog_delete', true)
        },

        confirm_del (x) {
            this.$store.dispatch('info/del', {id:x.data})
        },

        select (x) {
            this.$store.commit('info/set_selected_info', x)
        },

        search () {
            return this.$store.dispatch('info/search', {})
        },

        change_page(x) {
            this.curr_page = x
            this.$store.dispatch('info/search', {})
        },

        pin (x) {
            this.select(x)
            this.$store.dispatch('info/pin')
        },

        get (x) {
            this.select(x)
            this.$store.commit('infoPreview/set_object', ['info_id', x.info_id])
            this.$store.dispatch('infoPreview/gets').then((x) => {
                this.$store.commit('infoPreview/set_object', ['dialog', true])
            })
        }
    }
}
</script>
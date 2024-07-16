<template>
    <v-dialog
        v-model="dialog"
        scrollable
        :overlay="false"
        max-width="1000px"
        transition="dialog-transition"
        content-class="dialog-wa"
    >
        <v-card>
            <v-card-title primary-title class="cyan white--text py-1 pr-1">
                <v-layout row wrap>
                    <v-flex class="pt-2">
                        <h3>WA TEMPLATES</h3>        
                    </v-flex>
                    <v-flex class="text-xs-right">
                        <v-text-field
                            solo
                            hide-details
                            placeholder="Pencarian" 
                            v-model="query"
                            @keyup.enter="do_search($event)"
                        >
                            <template v-slot:append-outer>
                                <v-btn color="primary" class="ma-0 btn-icon" @click="do_search">
                                    <v-icon>search</v-icon>
                                </v-btn>
                                <v-btn color="red" class="ma-0 ml-2 btn-icon white--text" @click="dialog=!dialog">
                                    <v-icon>close</v-icon>
                                </v-btn> 
                            </template>
                        </v-text-field>
                    </v-flex>
                </v-layout>
                
            </v-card-title>
            <v-card-text pa-2>
                <v-layout row wrap class="wa-container">
                    <v-flex xs3 v-for="(wa, n) in watemplates" v-bind:key="n" class="mb-2 pr-2">
                        <v-card>
                            <v-card-title primary-title class="pa-0">
                                <v-layout row wrap>
                                    <v-flex>
                                        <v-chip :color="wa.color_class" :text-color="wa.color_text_class">
                                            {{wa.watemplate_name}}</v-chip>
                                    </v-flex>
                                    <v-flex class="text-xs-right">
                                        <v-chip color="green" text-color="white" @click="wame(wa)">
                                            <v-icon small>send</v-icon></v-chip>
                                    </v-flex>
                                </v-layout>
                                
                            </v-card-title>
                            <v-card-text class="pa-2">
                                
                                <v-layout row wrap>
                                    <v-flex xs12>
                                        {{wa.watemplate_content}}
                                    </v-flex>
                                </v-layout>
                            </v-card-text>
                        </v-card>
                    </v-flex>
                </v-layout>
                
                <!-- <v-data-table 
                    :headers="headers"
                    :items="watemplates"
                    :loading="false"
                    hide-actions
                    class="elevation-1">
                    <template slot="items" slot-scope="props">
                        
                        
                        <td class="text-xs-left pa-2" @click="select(props.item)">
                        <v-chip :color="props.item.color_class" :text-color="props.item.color_text_class">{{ props.item.watemplate_name }}</v-chip>
                        </td>   
                        <td class="text-xs-left pa-2" @click="select(props.item)">{{ props.item.watemplate_content }}</td>                 
                        
                        <td class="text-xs-center pa-0" @click="select(props.item)">
                            <v-btn color="primary" class="btn-icon ma-0" small><v-icon>create</v-icon></v-btn>
                        </td>
                    </template>
                </v-data-table>
                <v-divider></v-divider>
                <v-pagination
                    style="margin-top:10px;margin-bottom:10px"
                    v-model="curr_page"
                    :length="xtotal_page"
                    @input="change_page"
                ></v-pagination> -->
            </v-card-text>
        </v-card>
    </v-dialog>
</template>

<style>
.input-dense .v-input__control {
    min-height: 36px !important;
}

.dialog-wa .wa-container .v-card .v-card__text {
    max-height: 180px;
    overflow-y: scroll;
}
.dialog-wa .v-text-field.v-text-field--solo .v-input__control {
    min-height: 36px;
}
.dialog-wa .v-text-field.v-text-field--solo .v-input__append-outer {
    margin-top: 0px;
    margin-left: 0px;
}
</style>
<script>
module.exports = {
    components : {
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
                    text: "PREVIEW KONTEN",
                    align: "left",
                    sortable: false,
                    width: "78%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "ACTION",
                    align: "center",
                    sortable: false,
                    width: "7%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                }
            ]
        }
    },

    computed : {
        dialog : {
            get () { return this.$store.state.post_sales.dialog_wa },
            set (v) { this.$store.commit('post_sales/set_common', ['dialog_wa', v]) }
        },
        
        watemplates () {
            return this.$store.state.watemplate.watemplates
        },

        watemplate_id () {
            return this.$store.state.watemplate.selected_watemplate.watemplate_id
        },

        query : {
            get () { return this.$store.state.watemplate.search },
            set (v) { this.$store.commit('watemplate/update_search', v) }
        },

        curr_page : {
            get () { return this.$store.state.watemplate.current_page },
            set (v) { this.$store.commit('watemplate/update_current_page', v) }
        },

        xtotal_page () {
            return this.$store.state.watemplate.total_watemplate_page
        },
        
        query : {
            get () { return this.$store.state.watemplate.search },
            set (v) { this.$store.commit('watemplate/set_common', ['search', v]) }
        } 
    },

    methods : {
        save () {
            this.$store.dispatch('watemplate_new/save')
        },

        select (x) {
            this.$store.commit('watemplate/set_selected_watemplate', x)
        },

        wame (y) {
            
            if (!!this.$store.state.post_sales.broadcast)
                return this.wabroadcast(y)

            let x = this.$store.state.post_sales.selected_order

            this.select(y)
            this.$store.commit('post_sales/set_object', ['selected_watemplate', y])
            this.$store.commit('post_sales/set_object', ['watzap_destination', x.customer_phone])

            this.$store.dispatch('post_sales/wa_log');

            wa_url = this.$store.state.system.wa_url
            this.$store.commit('post_sales/set_object', ['watzap_img', y.img_url])
            this.$store.dispatch('post_sales/wa_format', {content:y.watemplate_content,order:x}).then(res => {
                this.$store.commit('post_sales/set_object', ['watzap_text', res])
                this.$store.commit('post_sales/set_object', ['dialog_wa_send', true])
                
                // this.$store.dispatch('post_sales/watzap')

                // window.open(wa_url + "?phone=" + x.customer_phone + "&text=" + res + "&type=phone_number")
            })

            // this.select(y)

            // // let x = this.$store.state.post_sales.selected_order
            // wa_url = this.$store.state.system.wa_url
            // this.$store.dispatch('post_sales/wa_format', {content:y.watemplate_content_send,order:x}).then(res => {
            //     window.open(wa_url + "?phone=" + x.customer_phone + "&text=" + res + "&type=phone_number")
            // })
            // window.open(wa_url + "?phone=" + x.customer_phone + "&text=" + y.watemplate_content_send + "&type=phone_number")
        },

        wabroadcast (y) {
            let x = this.$store.state.post_sales.selected_order

            this.$store.commit('post_sales/set_object', ['selected_watemplate', y])
            this.$store.commit('post_sales/set_object', ['limit', 999])

            // this.$store.dispatch('post_sales/wa_format', {content:y.watemplate_content,order:x}).then(res => {
                this.$store.commit('broadcast/set_object', ['watzap_text', y.watemplate_content])
                this.$store.commit('broadcast/set_object', ['watzap_img', y.img_url?y.img_url:''])
                this.$store.commit('post_sales/set_object', ['dialog_broadcast', true])

                this.$store.dispatch('post_sales/search').then(resx => {
                    console.log(resx)
                    let recs_index = {}
                    let recs = []
                    for (let resy of resx.records) {
                        recs_index[resy.customer_phone] = {status:null}
                        recs.push({
                            no:resy.customer_phone,
                            name:resy.customer_name,
                            customer_name:resy.customer_name,
                            customer_phone:resy.customer_phone,
                            admin_name:resy.admin_name,
                            admin_phone:resy.admin_phone
                        })
                    }
                        
                    this.$store.commit('broadcast/set_object', ['recipients', recs])
                    this.$store.commit('broadcast/set_object', ['recipients_index', recs_index])
                    this.$store.commit('post_sales/set_object', ['limit', 10])
                })

                
                // this.$store.dispatch('post_sales/watzap')

                // window.open(wa_url + "?phone=" + x.customer_phone + "&text=" + res + "&type=phone_number")
            // })
        },

        do_search() {
            this.$store.dispatch('watemplate/search', {})
        }
    },

    mounted () {
        this.$store.dispatch('watemplate/search', {special:'NONE'})
    },

    watch : {}
}
</script>
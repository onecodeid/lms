<template>
    <v-card>
        <v-card-text>
            <v-layout row wrap>
                <v-flex xs6>
                    <v-chip color="indigo" text-color="white" v-if="!!selected_profile">
                        <v-avatar>
                            <v-icon>account_circle</v-icon>
                        </v-avatar>
                        {{ selected_profile.profile_name }}
                    </v-chip>
                </v-flex>
                <v-flex xs6>
                    <v-layout column>
                        <v-flex class="text-xs-right" pb-1>
                            <v-btn color="cyan" class="btn-icon ma-0 ml-1 white--text" depressed small>Broadcast</v-btn>

                            <v-btn :color="t.color_class" class="btn-icon ma-0 ml-1" 
                                :class="t.color_text_class!=''?t.color_text_class+'--text':''" depressed small
                                v-for="(t, n) in watemplates" :key="n"
                                :title="t.watemplate_name"
                                @click="wabroadcast(t)"
                                v-if="n<3">{{t.watemplate_code}}</v-btn>
                            
                            <v-btn color="grey" class="btn-icon ma-0 ml-1 white--text" depressed small title="Whatsapp templates" @click="waopen()">WA</v-btn>
                        </v-flex>
                    </v-layout>
                </v-flex>
            </v-layout>

            <v-data-table 
                :headers="headers"
                :items="orders"
                :loading="false"
                hide-actions
                class="elevation-1">
                
                <template slot="items" slot-scope="props">
                    <td class="text-xs-left pa-2" @click="select(props.item)">
                        <div><b>{{ props.item.customer_name }}</b> — <span class="grey--text">{{ props.item.level_name }}, {{ props.item.city_name }}</span></div>
                        <div>
                            <v-layout row wrap>
                                <v-flex><v-icon small>smartphone</v-icon> <a :href="wa_url + '?phone=' + props.item.customer_phone" target="_blank">{{props.item.customer_phone}}</a></v-flex>
                                <!-- <v-flex class="text-xs-right green--text">{{props.item.walast_date}}</v-flex> -->
                            </v-layout>
                            
                        </div>
                        <div class="purple--text" v-if="props.item.L_SoIsDS == 'Y'">— Dropship, {{ props.item.ds_customer_name }} - {{ props.item.ds_city_name}}</div></td>
                    <td>
                        <v-layout row wrap>
                            <v-flex xs12 v-for="(d,n) in props.item.details" :key="props.item.customer_id+'-'+n" class="pa-1"
                                :class="{'grey lighten-3':n%2==1}" @click="select(props.item);select_sub(d)">
                                <v-layout row wrap>
                                    <v-flex xs12>
                                        [{{d.so_date}}] 
                                        <span class="cyan--text"><a href="javascript:;">[{{d.so_number}}]</a></span> 
                                            [Rp <b>{{one_money(d.so_total)}}</b>]        
                                    </v-flex>
                                    <v-flex xs12>
                                        — {{ d.so_expedition}}, {{d.so_service}}
                                    </v-flex>
                                </v-layout>
                                
                            </v-flex>
                        </v-layout>
                    </td>

                    <td class="text-xs-center">
                        <v-layout column>
                            <v-flex>
                                <v-btn :color="t.color_class" class="btn-icon ma-0 ml-1" 
                                    :class="t.color_text_class!=''?t.color_text_class+'--text':''" depressed small
                                    v-for="(t, n) in watemplates" :key="n"
                                    :title="t.watemplate_name"
                                    @click="wame(props.item, t)"
                                    v-if="n<3">{{t.watemplate_code}}</v-btn>
                                
                                <v-btn color="grey" class="btn-icon ma-0 ml-1 white--text" depressed small title="Whatsapp templates" @click="waopen(props.item)">WA</v-btn>
                            </v-flex>
                            <v-flex class="green white--text caption mt-1" v-show="!!props.item.walast_date">
                                Last WA : {{props.item.walast_date}}
                            </v-flex>
                        </v-layout>
                        
                        <!-- <v-menu offset-y>
                            <template v-slot:activator="{ on }">
                                <v-btn color="grey" class="btn-icon ma-0 ml-1 white--text" depressed small title="Whatsapp templates" v-on="on" @click="waopen()">WA</v-btn>
                            </template>
                            <v-list>
                                <v-list-tile
                                v-for="(item, index) in items"
                                :key="index"
                                @click=""
                                >
                                <v-list-tile-title>{{ item.title }}</v-list-tile-title>
                                </v-list-tile>
                            </v-list>
                        </v-menu> -->
                        
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

        <v-snackbar
            v-model="snackbar"
            :timeout=3000
            top
            >
            {{snacktext}}
            <v-btn
                color="pink"
                flat
                @click="snackbar = false"
            >
                Tutup
            </v-btn>
        </v-snackbar>
    </v-card>
</template>

<script>
module.exports = {
    components : {

    },

    data () {
        return {
            items: [
                { title: 'Click Me' },
                { title: 'Click Me' },
                { title: 'Click Me' },
                { title: 'Click Me 2' }
            ],
            headers: [
                {
                    text: "NAMA CUSTOMER",
                    align: "left",
                    sortable: false,
                    width: "30%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },

                {
                    text: "TRANSAKSI",
                    align: "left",
                    sortable: false,
                    width: "45%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },

                {
                    text: "ACTION",
                    align: "center",
                    sortable: false,
                    width: "25%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                }
            ]
        }
    },

    computed : {
        orders() {
            return this.$store.state.post_sales.orders
        },

        selected_order: {
            get () { return this.$store.state.post_sales.selected_order },
            set (v) { this.$store.commit('post_sales/set_object', ['selected_order', v]) }
        },

        selected_sub_order: {
            get () { return this.$store.state.post_sales.selected_sub_order },
            set (v) { this.$store.commit('post_sales/set_object', ['selected_sub_order', v]) }
        },

        curr_page : {
            get () { return this.$store.state.post_sales.current_page },
            set (v) { 
                this.$store.commit('post_sales/set_common', ['current_page', v])
                this.$store.dispatch('post_sales/search')
            }
        },

        xtotal_page () {
            return this.$store.state.post_sales.total_page
        },

        search_summaries () {
            
        },

        watemplates () {
            return this.$store.state.post_sales.watemplates
        },

        selected_profile () { return this.$store.state.post_sales.selected_profile },

        wa_url () {
            return this.$store.state.system.wa_url
        },

        snackbar : {
            get () { return this.$store.state.post_sales.snackbar },
            set (v) { this.$store.commit('post_sales/set_common', ["snackbar", v]) }
        },

        snacktext : {
            get () { return this.$store.state.post_sales.snacktext },
            set (v) { this.$store.commit('post_sales/set_common', ["snacktext", v]) }
        },

        watzap () {
            return this.$store.state.post_sales.watzap
        },

        watzap_numbers () {
            return this.$store.state.post_sales.watzap_numbers
        }
    },

    methods: {
        one_money (x) {
            return window.one_money(x)
        },

        change_page(x) {
            this.curr_page = x
            this.$store.dispatch('salesorder/search')
        },

        select (x) {
            this.$store.commit('post_sales/set_object', ['selected_order', x])
        },

        select_sub (x) {
            this.selected_sub_order = x
            this.$store.commit('post_sales/set_object', ['dialog_order', true])
        },

        wame (x, y) {
            this.select(x)
            this.$store.commit('post_sales/set_object', ['selected_watemplate', y])
            this.$store.commit('post_sales/set_object', ['watzap_destination', x.customer_phone])

            this.$store.dispatch('post_sales/wa_log');

            wa_url = this.wa_url
            this.$store.commit('post_sales/set_object', ['watzap_img', y.img_url?y.img_url:''])
            this.$store.dispatch('post_sales/wa_format', {content:y.watemplate_content,order:x}).then(res => {
                this.$store.commit('post_sales/set_object', ['watzap_text', res])
                this.$store.commit('post_sales/set_object', ['dialog_wa_send', true])
                
                // this.$store.dispatch('post_sales/watzap')

                // window.open(wa_url + "?phone=" + x.customer_phone + "&text=" + res + "&type=phone_number")
            })
            // window.open(wa_url + "?phone=" + x.customer_phone + "&text=" + y.watemplate_content_send + "&type=phone_number")
        },

        waopen (x) {
            let broadcast = false
            if (!!x)
                this.select(x)
            else
                broadcast = true

            this.$store.commit('post_sales/set_object', ['broadcast', broadcast])
            this.$store.commit('post_sales/set_object', ['dialog_wa', true])
        },

        wabroadcast (x) {
            this.$store.commit('post_sales/set_object', ['selected_watemplate', x])
            this.$store.commit('post_sales/set_object', ['limit', 999])


            // this.$store.dispatch('post_sales/wa_format', {content:x.watemplate_content,order:x}).then(res => {
            //     this.$store.commit('broadcast/set_object', ['watzap_text', res])
                this.$store.commit('broadcast/set_object', ['watzap_text', x.watemplate_content])
                this.$store.commit('broadcast/set_object', ['watzap_img', x.img_url?x.img_url:''])
                this.$store.commit('post_sales/set_object', ['dialog_broadcast', true])

                this.$store.dispatch('post_sales/search').then(resx => {
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
                            admin_phone:resy.admin_phone})
                    }
                        
                    this.$store.commit('broadcast/set_object', ['recipients', recs])
                    this.$store.commit('broadcast/set_object', ['recipients_index', recs_index])
                    this.$store.commit('post_sales/set_object', ['limit', 10])
                })

                
                // this.$store.dispatch('post_sales/watzap')

                // window.open(wa_url + "?phone=" + x.customer_phone + "&text=" + res + "&type=phone_number")
            // })
        }
    },

    mounted () {
        this.$store.dispatch('post_sales/search_watemplate')
        this.$store.dispatch('post_sales/get_watzap')
    }
}
</script>
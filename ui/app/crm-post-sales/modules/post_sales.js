import { one_token } from "../../assets/js/global.js"
import { shortcodes, wa_format } from "../../assets/js/crm.js"

export default {
    namespaced: true,
    state: {
        search_status: 0,
        search_error_message: '',
        search: '',

        customer_types: [
            { id: 'A', text: 'Semua Customer' },
            { id: 'Y', text: 'Customer Baru' },
            { id: 'N', text: 'Repeat Order' }
        ],
        selected_customer_type: null,

        sales_types: [
            { id: '0', text: 'Semua Penjualan' },
            { id: '4', text: 'Menunggu Pembayaran' },
            { id: '5,6,7,8', text: 'Proses Pengiriman' },
            { id: '9,11', text: 'Penjualan yang Sudah Selesai' }
        ],
        selected_sales_type: null,

        salesdurs: [],
        selected_salesdur: null,
        custom_date: false,
        custom_sdate: window.local_date,
        custom_edate: window.local_date,

        expeditions: [],
        selected_expedition: null,

        services: [],
        selected_service: null,

        provinces: [],
        selected_province: [],
        search_province: '',

        cities: [],
        selected_city: [],
        search_city: '',

        items: [],
        selected_item: [],

        packets: [],
        selected_packet: [],

        leadsources: [],
        selected_leadsource: null,

        watemplates: [],
        selected_watemplate: null,
        watemplate_query: "",

        orders: [],
        total_order: 0,
        selected_order: null,
        selected_sub_order: null,
        current_page: 1,
        total_page: 1,

        current_page: 1,
        selected_main_tab: null,
        limit: 10,

        /**
         * Profiles
         */
        profiles: [],
        selected_profile: null,

        profile_id: 0,
        profile_name: "",
        profile_json: "",
        dialog_new: false,
        dialog_wa: false,
        dialog_wa_send: false,
        dialog_order: false,

        profile_total_page: 1,
        profile_current_page: 1,
        /** End of Profiles */

        /**
         * Save states
         */
        save_step: 0,
        save_to: 0,

        /**
         * WATZAP
         */
        watzap_text: "",
        watzap_img: "",
        watzap: null,
        watzap_numbers: [],
        selected_watzap_number: null,
        watzap_destination: "",
        snackbar: false,
        snacktext: "",
        broadcast: false,

        /**
         * BROADCAST
         */
        dialog_broadcast: false
    },

    mutations: {
        set_common(state, v) {
            let name = v[0]
            let val = v[1]
            if (typeof(val) == "string")
                eval(`state.${name} = "${val}"`)
            else
                eval(`state.${name} = ${val}`)
        },

        set_object(state, v) {
            state[v[0]] = v[1]
        }
    },

    actions: {
        collect(context, tmp) {
            let items = []
            for (let item of context.state.selected_item)
                items.push(item.M_ItemID)

            let packets = []
            for (let packet of context.state.selected_packet)
                packets.push(packet.M_PacketID)

            let provinces = []
            if (!!context.state.selected_province)
                for (let province of context.state.selected_province)
                    provinces.push(province.M_ProvinceID)

            let cities = []
            if (!!context.state.selected_city)
                for (let city of context.state.selected_city)
                    cities.push(city.M_CityID)

            let expeditions = []
            if (!!context.state.selected_expedition)
                for (let expedition of context.state.selected_expedition)
                    expeditions.push(expedition.M_ExpeditionID)

            let services = []
            if (!!context.state.selected_service)
                for (let service of context.state.selected_service)
                    services.push(service.service)

            let prm = {
                token: one_token(),
                search: "",
                page: context.state.current_page,
                customer_status: context.state.selected_customer_type.id,
                sales_status: context.state.selected_sales_type.id,
                start_date: context.state.selected_salesdur.start_date,
                end_date: context.state.selected_salesdur.end_date,
                items: items.join(","),
                packets: packets.join(","),
                provinces: provinces,
                cities: cities,
                expeditions: expeditions,
                services: services,
                leadsource: context.state.selected_leadsource ? context.state.selected_leadsource.leadsource_id : 0,
                limit: context.state.limit
            }

            if (!!context.state.custom_date) {
                prm.start_date = context.state.custom_sdate
                prm.end_date = context.state.custom_edate
            }

            if (!!tmp) {
                delete prm.token
                let ctx = context.state
                prm.period = {
                    id: ctx.selected_salesdur ? ctx.selected_salesdur.salesdur_id : 0,
                    custom: {
                        state: ctx.custom_date,
                        sdate: ctx.custom_sdate,
                        edate: ctx.custom_edate
                    }
                }
                prm = { id: context.state.save_to == 0 ? 0 : context.state.profile_id, name: context.state.profile_name, json: JSON.stringify(prm) }
            }

            return prm
        },

        async save(context) {
            context.dispatch("collect", true).then(res => {
                context.dispatch("system/postme", {
                    url: "crm/postsales/save_template",
                    prm: res,
                    callback: function(d) {
                        context.commit("set_common", ["dialog_new", false])
                        context.dispatch("search_profile")
                    },
                    failback: function(e) {
                        // context.commit('set_common', ['loading_city', false])
                    }
                }, { root: true })
            })
        },

        async search(context, tmp) {
            return context.dispatch("collect").then(res => {
                    res.return = true
                    return context.dispatch("system/postme", {
                        url: "crm/postsales/search",
                        prm: res,
                        callback: function(d) {
                            context.commit("set_object", ['orders', d.records])
                            context.commit("set_common", ["total_page", d.total_page])
                        },
                        failback: function(e) {
                            // context.commit('set_common', ['loading_city', false])
                        }
                    }, { root: true })
                })
                // return "X"
        },

        async search_salesdur(context) {
            // context.commit('set_common', ['loading_city', true])
            let prm = {
                token: one_token(),
                search: ""
            }

            context.dispatch("system/postme", {
                url: "crm/salesdur/search",
                prm: prm,
                callback: function(d) {
                    context.commit("set_object", ['salesdurs', d.records])

                    if (!context.state.selected_salesdur)
                        context.commit('set_object', ['selected_salesdur', d.records[0]])
                        // context.commit('set_common', ['loading_city', false])
                },
                failback: function(e) {
                    // context.commit('set_common', ['loading_city', false])
                }
            }, { root: true })
        },

        async search_expedition(context) {
            let prm = {
                token: one_token(),
                search: ""
            }

            context.dispatch("system/postme", {
                url: "master/expedition/search",
                prm: prm,
                callback: function(d) {
                    context.commit("set_object", ['expeditions', d.records])
                },
                failback: function(e) {
                    // context.commit('set_common', ['loading_city', false])
                }
            }, { root: true })
        },

        async search_service(context) {
            let prm = {
                token: one_token(),
                to: 1781,
                courier: context.state.selected_expedition[0].M_ExpeditionROCode,
                weight: 1
            }

            context.dispatch("system/postme", {
                url: "master/expedition/search_service",
                prm: prm,
                callback: function(d) {
                    let data = JSON.parse(d)
                    context.commit("set_object", ['services', data.rajaongkir.results[0].costs])
                        // context.commit("set_object", ["selected_service", null])
                },
                failback: function(e) {
                    // context.commit('set_common', ['loading_city', false])
                }
            }, { root: true })
        },

        async search_province(context) {
            let prm = {
                token: one_token(),
                search: context.state.search_province
            }

            context.dispatch("system/postme", {
                url: "master/province/search",
                prm: prm,
                callback: function(d) {
                    context.commit("set_object", ['provinces', d.records])
                    context.commit("set_object", ["selected_province", []])

                    context.commit("set_object", ['cities', []])
                    context.commit("set_object", ["selected_city", []])
                },
                failback: function(e) {
                    // context.commit('set_common', ['loading_city', false])
                }
            }, { root: true })
        },

        // export async function search_items(prm) {
        //     return ajaxPost(URL + 'master/item/search_w_price', prm)
        // }

        // export async function search_packets(prm) {
        //     return ajaxPost(URL + 'master/packet/search_w_price', prm)
        // }

        async search_city(context) {
            let prm = {
                token: one_token(),
                search: context.state.search_city,
                province_id: context.state.selected_province[0].M_ProvinceID
            }

            context.dispatch("system/postme", {
                url: "master/city/search",
                prm: prm,
                callback: function(d) {
                    context.commit("set_object", ['cities', d.records])
                    context.commit("set_object", ["selected_city", []])
                },
                failback: function(e) {
                    // context.commit('set_common', ['loading_city', false])
                }
            }, { root: true })
        },

        async search_item(context) {
            let prm = {
                token: one_token(),
                search: "",
                customer_level: 5
            }

            context.dispatch("system/postme", {
                url: "master/item/search_w_price",
                prm: prm,
                callback: function(d) {
                    context.commit("set_object", ['items', d.records])
                    context.commit("set_object", ["selected_item", []])
                },
                failback: function(e) {
                    // context.commit('set_common', ['loading_city', false])
                }
            }, { root: true })
        },

        async search_packet(context) {
            let prm = {
                token: one_token(),
                search: "",
                customer_level: 5
            }

            context.dispatch("system/postme", {
                url: "master/packet/search_w_price",
                prm: prm,
                callback: function(d) {
                    context.commit("set_object", ['packets', d.records])
                    context.commit("set_object", ["selected_packet", []])
                },
                failback: function(e) {
                    // context.commit('set_common', ['loading_city', false])
                }
            }, { root: true })
        },

        async search_leadsource(context) {
            context.commit('set_common', ['save', true])
            let prm = {
                token: one_token(),
                limit: 999,
                search: ''
            }

            context.dispatch("system/postme", {
                url: "master/leadsource/search",
                prm: prm,
                callback: function(d) {
                    context.commit('set_object', ['leadsources', d.records])
                    context.commit('set_object', ['selected_leadsource', null])
                }
            }, { root: true })

        },

        async search_watemplate(context) {
            context.commit('set_common', ['save', true])
            let prm = {
                token: one_token(),
                limit: 44,
                search: context.state.watemplate_query,
                page: 1,
                pin: 'Y'
            }

            context.dispatch("system/postme", {
                url: "crm/watemplate/search",
                prm: prm,
                callback: function(d) {
                    context.commit('set_object', ['watemplates', d.records])
                    context.commit('set_object', ['selected_watemplate', null])
                }
            }, { root: true })

        },

        async search_profile(context) {
            let prm = {
                token: one_token(),
                search: '',
                page: context.state.profile_current_page
            }

            context.dispatch("system/postme", {
                url: "crm/postsales/search_profile",
                prm: prm,
                callback: function(d) {
                    context.commit('set_object', ['profiles', d.records])
                    context.commit('set_object', ['profile_total_page', d.total_page])
                }
            }, { root: true })
        },

        async del_profile(context, prm) {
            prm.token = one_token()

            context.dispatch("system/postme", {
                url: "crm/postsales/delete_profile",
                prm: prm,
                callback: function(d) {
                    context.dispatch('search_profile')
                }
            }, { root: true })
        },

        wa_format(context, data) {
            let text = wa_format(shortcodes, {
                    content: data.content,
                    params: data.order
                })
                // let text = data.content.replace(/(\[customer\_name\])/g, data.order.customer_name)
                //     .replace(/(\[company\_name\])/gi, "Zalfa Natural")
                //     .replace(/(\[admin\_name\])/gi, data.order.admin_name)
                //     .replace(/(\[admin\_phone\])/gi, data.order.admin_phone)

            return text
        },

        async wa_log(context) {
            let prm = {
                customer_id: context.state.selected_order.customer_id,
                wa_id: context.state.selected_watemplate.watemplate_id,
                content: context.state.selected_watemplate.watemplate_content_send
            }

            context.dispatch("system/postme", {
                url: "crm/walast/log",
                prm: prm,
                callback: function(d) {
                    console.log(d)
                }
            }, { root: true })
        },

        async watzap(context) {
            let prm = {
                token: one_token(),
                key: context.state.selected_watzap_number.key,
                no: context.state.watzap_destination,
                text: context.state.watzap_text
            }

            if (context.state.watzap_img != "")
                prm.img_url = context.state.watzap_img

            return context.dispatch("system/postme", {
                url: "crm/watzapid/send_message",
                prm: prm,
                callback: function(d) {
                    context.commit("set_common", ["snacktext", "Pesan WA berhasil terkirim !"])
                    context.commit("set_common", ["snackbar", true])

                    return d
                }
            }, { root: true })
        },

        async get_watzap(context) {
            context.dispatch("system/postme", {
                url: "crm/watzapid/get_conf",
                prm: {},
                callback: function(d) {
                    context.commit("set_object", ["watzap", d])

                    if (!!d.licenses_key)
                        context.commit("set_object", ["watzap_numbers", d.licenses_key])
                },
                failback: function(e) {
                    // context.commit('set_common', ['loading_city', false])
                }
            }, { root: true })

        }
    }
}
import { one_token, one_user } from "../../assets/js/global.js"
import { shortcodes, wa_format } from "../../assets/js/crm.js"

export default {
    namespaced: true,
    state: {
        search_status: 0,
        search_error_message: '',
        search: '',
        one_user: one_user(),
        shortcodez: shortcodes,

        intervals: [{ id: 10, label: '10 detik' }, { id: 20, label: '20 detik' }, { id: 30, label: '30 detik' }, { id: 60, label: '1 menit' }, { id: 120, label: '2 menit' }],
        selected_interval: null,

        cnt: 3,
        recipients: [],
        recipients_index: {},

        /**
         * Timing
         */
        n: 0,
        l: 0,
        timeout: 0,

        /**
         * WATZAP
         */
        watzap_text: "",
        watzap_img: "",
        watzap: null,
        watzap_numbers: [],
        watzap_destinations: [],
        selected_watzap_number: null,
        snackbar: false,
        snacktext: ""
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

        },

        async broadcast(context) {


            setTimeout(function() {
                let timeout = context.state.timeout
                let interval = context.state.selected_interval.id

                let n = context.state.n
                let l = context.state.recipients.length
                let destinations = []

                // console.log("timeout:" + timeout)
                // console.log("destination:" + context.state.recipients[n].customer_phone)
                if (timeout == 0 && n < l) {
                    destinations = []
                    let rec = context.state.recipients_index

                    for (let i = n; i < (Math.round(n) + Math.round(context.state.cnt)); i++) {
                        if (context.state.recipients[i]) {
                            let z = context.state.recipients[i]
                                // let text = context.dispatch("wa_format", { content: context.state.watzap_text, params: z })
                            let text = wa_format(context.state.shortcodez, { content: context.state.watzap_text, params: z })
                            destinations.push({
                                number: z.no,
                                text: text
                            })

                            rec[context.state.recipients[i].no].status = "100"
                        }
                    }

                    context.commit("set_object", ["watzap_destinations", destinations])

                    context.dispatch("watzap").then(res => {
                        let rec = context.state.recipients_index
                        for (let r of res) {
                            rec[r.no].status = r.status
                        }
                        console.log(rec)
                        context.commit("set_object", ["recipients_index", rec])
                    })

                    timeout = interval
                    n = n + Math.round(context.state.cnt)
                    context.commit("set_object", ["timeout", timeout])
                }

                // console.log("n:" + n + ", l:" + l)

                if (timeout > 0)
                    context.dispatch("broadcast")

                timeout = timeout - 1
                context.commit("set_object", ["timeout", timeout])
                context.commit("set_object", ["n", n])
            }, 1000)

        },

        async watzap(context) {
            let prm = {
                token: one_token(),
                key: context.state.selected_watzap_number.key,
                destinations: context.state.watzap_destinations,
                return: true
            }

            if (context.state.watzap_img != "")
                prm.img_url = context.state.watzap_img

            return context.dispatch("system/postme", {
                url: "crm/watzapid/send_multis",
                prm: prm,
                callback: function(d) {
                    context.commit("set_common", ["snacktext", "Pesan WA berhasil terkirim !"])
                    context.commit("set_common", ["snackbar", true])

                    return d
                }
            }, { root: true })
        },

        wa_format(context, data) {
            // let shortcodes = {
            //     "[customer_name]": "name",
            //     "[customer_phone]": "phone",
            //     "[admin_name]": "admin_name",
            //     "[admin_phone]": "admin_phone"
            // }
            let shortcodes = context.rootState.watemplate.shortcodes
            let rst = data.content
            let x
            for (let sh of shortcodes) {
                x = "[" + sh + "]"
                let replacement = !!data.params[sh] ? data.params[sh] : ''

                while (rst.includes(x)) {
                    rst = rst.replace(x, replacement)
                }

            }

            return rst
        }
    }
}
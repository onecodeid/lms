// 1 => LOADING
// 2 => DONE
// 3 => ERROR
import * as api from "./api_info.js"
import { one_token, insertAtCursor } from "../../assets/js/global.js"

export default {
    namespaced: true,
    state: {
        search_status: 0,
        search_error_message: '',
        search: '',

        infos: [],
        total_info: 0,

        info_title: '',
        info_content: '',
        info_img: '',
        info_img_url: '',
        // info_img_uri: '',

        info_sdate: '',
        info_edate: '',

        colors: [],
        selected_color: null,

        users: [],
        selected_users: [],
        who_read: [],

        customer_levels: [],
        selected_customer_levels: [],
        customer_read_cnt: 0,

        gpt_types: [{ id: 1, text: "Pengumuman" }, { id: 2, text: "Ucapan Selamat" }, { id: 3, text: "Promo Produk" }],
        selected_gpt_type: null,
        gpt_topic: '',
        dialog_gpt: false,

        dialog_new: false
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

        set_object(state, val) {
            state[val[0]] = val[1]
        },

        update_search_error_message(state, msg) {
            state.search_error_message = msg
        },

        update_search_status(state, val) {
            state.search_status = val
        },

        set_content(state, val) {
            state.info_content = val
        }
    },
    actions: {
        async save(context, fn) {
            context.commit("update_search_status", 1)
            try {
                let prm = {}
                prm.token = one_token()
                prm.hdata = {
                    m_title: context.state.info_title,
                    m_content: context.state.info_content,
                    m_img: context.state.info_img,
                    m_sdate: context.state.info_sdate,
                    m_edate: context.state.info_edate
                }

                // RECIPIENTS
                let recipients = []
                for (let r of context.state.selected_users) {
                    if (!!r.group) recipients.push({ user_id: 0, group_id: r.id, level_id: 0 })
                    else recipients.push({ user_id: r.id, group_id: 0, level_id: 0 })
                }
                for (let r of context.state.selected_customer_levels)
                    recipients.push({ user_id: 0, group_id: 0, level_id: r.level_id })
                    // END OF RECIPIENTS

                prm.hdata.recipients = recipients
                prm.hdata = JSON.stringify(prm.hdata)

                if (context.state.edit)
                    prm.message_id = context.rootState.info.selected_info.info_id

                let resp = await api.save(prm)
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")

                    if (fn) {
                        fn()
                    } else
                        context.dispatch('info/search', {}, { root: true })
                    context.commit('set_common', ['dialog_new', false])
                }
                return resp
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
            }
        },

        async search_color(context) {
            let prm = {
                token: one_token(),
                limit: 999,
                search: ''
            }

            context.dispatch("system/postme", {
                url: "master/color/search",
                prm: prm,
                callback: function(d) {
                    context.commit('set_object', ['colors', d.records])
                }
            }, { root: true })

        },

        async getBase64Image(context) {
            let prm = {
                token: one_token(),
                image: context.state.info_img
            }

            context.dispatch("system/postme", {
                url: "notice/message/get_img_md5",
                prm: prm,
                callback: function(d) {
                    context.commit("set_object", ["info_img", d])
                }
            }, { root: true })

            // const img = new Image();
            // img.crossOrigin = "anonymous"
            // img.src = context.state.info_img_url

            // img.onload = function() {
            //     const canvas = document.createElement("canvas");
            //     canvas.width = img.width;
            //     canvas.height = img.height;
            //     const ctx = canvas.getContext("2d");
            //     ctx.drawImage(img, 0, 0, img.width, img.height);
            //     const dataURL = canvas.toDataURL();
            //     // console.log(dataURL);

            //     context.commit("set_object", ["info_img", dataURL])
            //     return dataURL
            // };

            // var canvas = document.createElement("canvas");
            // canvas.width = img.width;
            // canvas.height = img.height;
            // var ctx = canvas.getContext("2d");
            // ctx.drawImage(img, 0, 0);
            // var dataURL = canvas.toDataURL("image/jpg");
            // return dataURL

            // return dataURL.replace(/^data:image\/?[A-z]*;base64,/);
        },

        async search_user(context) {
            let prm = {
                token: one_token(),
                search: ''
            }

            context.dispatch("system/postme", {
                url: "systm/user/search_dd_with_group",
                prm: prm,
                callback: function(d) {
                    context.commit('set_object', ['users', d])
                }
            }, { root: true })

        },

        async getGpt(context) {
            // const axios = require('axios');

            const prompt = 'Tuliskan sebuah contoh ' + context.state.selected_gpt_type.text +
                ' dengan topik ' + context.state.gpt_topic + ' dengan judul dan konten dan format json {"title":"","content":""}';
            const apiKey = '';
            const apiEndpoint = 'https://api.openai.com/v1/completions';

            axios.post(apiEndpoint, {
                prompt: prompt,
                temperature: 0.5,
                max_tokens: 250,
                model: "text-davinci-003"
            }, {
                headers: {
                    'Authorization': `Bearer ${apiKey}`,
                    'Content-Type': 'application/json'
                }
            }).then(response => {
                let d = response.data.choices[0].text
                console.log(d)
                d = JSON.parse(d)
                context.commit("set_object", ["info_title", d.title])
                context.commit("set_object", ["info_content", d.content])
                context.commit("set_object", ["dialog_gpt", false])
            }).catch(error => {
                console.log(error.response.data);
            });
        },

        async getGpt2(context) {

            const API_ENDPOINT = 'https://api.openai.com/v1/engines/davinci-codex/completions';

            const prompt = 'Hello, ';
            const apiKey = '';

            // axios.post(API_ENDPOINT, {
            //     prompt: prompt,
            //     max_tokens: 10,
            //     n: 1,
            //     stop: '\n',
            // }, {
            //     headers: {
            //         'Content-Type': 'application/json',
            //         'Authorization': `Bearer ${apiKey}`,
            //     },
            // }).then(response => {
            //     console.log(response.data.choices[0].text);
            // }).catch(error => {
            //     console.log(error.response.data);
            // });

            let prm = {
                prompt: prompt,
                max_tokens: 10,
                n: 1,
                stop: '\n',
            }

            context.dispatch("system/postme", {
                url: API_ENDPOINT,
                prm: prm,
                hdr: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${apiKey}`,
                },
                callback: function(d) {
                    console.log(d)
                }
            }, { root: true })

        },

        async search_customer_level(context) {
            let prm = {
                token: one_token(),
                search: ''
            }

            context.dispatch("system/postme", {
                url: "master/customerlevel/search",
                prm: prm,
                callback: function(d) {
                    context.commit('set_object', ['customer_levels', d.records])
                }
            }, { root: true })

        }

        //   var base64 = getBase64Image(document.getElementById("imageid"));
    }
}
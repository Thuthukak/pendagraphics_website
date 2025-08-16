import { unref, withCtx, createVNode, toDisplayString, useSSRContext } from "vue";
import { ssrRenderComponent, ssrInterpolate, ssrRenderAttr, ssrRenderStyle } from "vue/server-renderer";
import { _ as _export_sfc, N as Navbar } from "./Navbar-B2dw1uDe.js";
import { F as Footer } from "./Footer-Cv32ksWL.js";
import { g as ge } from "../ssr.js";
import "axios";
import "@vue/server-renderer";
import "@inertiajs/core";
import "es-toolkit";
import "es-toolkit/compat";
import "@inertiajs/core/server";
import "qs";
import "@fortawesome/fontawesome-svg-core";
import "@fortawesome/vue-fontawesome";
import "@fortawesome/free-solid-svg-icons";
import "@fortawesome/free-brands-svg-icons";
const _sfc_main = {
  __name: "DigitalMarketing",
  __ssrInlineRender: true,
  props: {
    seo: Object
  },
  setup(__props) {
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<!--[-->`);
      _push(ssrRenderComponent(Navbar, { seo: __props.seo }, null, _parent));
      _push(ssrRenderComponent(unref(ge), null, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<title data-v-8ece5e29${_scopeId}>${ssrInterpolate(__props.seo.title)}</title><meta name="description"${ssrRenderAttr("content", __props.seo.description)} data-v-8ece5e29${_scopeId}><meta name="keywords"${ssrRenderAttr("content", __props.seo.keywords)} data-v-8ece5e29${_scopeId}><link rel="canonical"${ssrRenderAttr("href", __props.seo.canonical_url)} data-v-8ece5e29${_scopeId}><meta property="og:title"${ssrRenderAttr("content", __props.seo.og_title)} data-v-8ece5e29${_scopeId}><meta property="og:description"${ssrRenderAttr("content", __props.seo.og_description)} data-v-8ece5e29${_scopeId}><meta property="og:image"${ssrRenderAttr("content", __props.seo.og_image)} data-v-8ece5e29${_scopeId}><meta property="og:url"${ssrRenderAttr("content", __props.seo.og_url)} data-v-8ece5e29${_scopeId}><meta property="og:type"${ssrRenderAttr("content", __props.seo.og_type)} data-v-8ece5e29${_scopeId}><meta property="og:site_name"${ssrRenderAttr("content", __props.seo.og_site_name)} data-v-8ece5e29${_scopeId}><meta name="twitter:card"${ssrRenderAttr("content", __props.seo.twitter_card)} data-v-8ece5e29${_scopeId}><meta name="twitter:title"${ssrRenderAttr("content", __props.seo.og_title)} data-v-8ece5e29${_scopeId}><meta name="twitter:description"${ssrRenderAttr("content", __props.seo.og_description)} data-v-8ece5e29${_scopeId}><meta name="twitter:image"${ssrRenderAttr("content", __props.seo.og_image)} data-v-8ece5e29${_scopeId}><meta name="robots" content="index, follow" data-v-8ece5e29${_scopeId}><meta name="viewport" content="width=device-width, initial-scale=1.0" data-v-8ece5e29${_scopeId}><meta name="theme-color" content="#0d6efd" data-v-8ece5e29${_scopeId}>`);
          } else {
            return [
              createVNode("title", null, toDisplayString(__props.seo.title), 1),
              createVNode("meta", {
                name: "description",
                content: __props.seo.description
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "keywords",
                content: __props.seo.keywords
              }, null, 8, ["content"]),
              createVNode("link", {
                rel: "canonical",
                href: __props.seo.canonical_url
              }, null, 8, ["href"]),
              createVNode("meta", {
                property: "og:title",
                content: __props.seo.og_title
              }, null, 8, ["content"]),
              createVNode("meta", {
                property: "og:description",
                content: __props.seo.og_description
              }, null, 8, ["content"]),
              createVNode("meta", {
                property: "og:image",
                content: __props.seo.og_image
              }, null, 8, ["content"]),
              createVNode("meta", {
                property: "og:url",
                content: __props.seo.og_url
              }, null, 8, ["content"]),
              createVNode("meta", {
                property: "og:type",
                content: __props.seo.og_type
              }, null, 8, ["content"]),
              createVNode("meta", {
                property: "og:site_name",
                content: __props.seo.og_site_name
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "twitter:card",
                content: __props.seo.twitter_card
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "twitter:title",
                content: __props.seo.og_title
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "twitter:description",
                content: __props.seo.og_description
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "twitter:image",
                content: __props.seo.og_image
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "robots",
                content: "index, follow"
              }),
              createVNode("meta", {
                name: "viewport",
                content: "width=device-width, initial-scale=1.0"
              }),
              createVNode("meta", {
                name: "theme-color",
                content: "#0d6efd"
              })
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`<section id="apply" class="cta" data-v-8ece5e29><div class="container px-5" data-aos="zoom-in" data-v-8ece5e29><div class="text-center text-white" data-v-8ece5e29><h1 class="fw-bold" data-v-8ece5e29>Digital Marketing That Delivers Results</h1><p data-v-8ece5e29>We craft targeted campaigns that boost visibility, drive traffic, and convert clicks into loyal customers — all tailored to your brand’s goals and audience..</p></div></div></section><section data-v-8ece5e29><div class="container mt-5 my-5 py-5 px-4" data-v-8ece5e29><div class="row" data-v-8ece5e29><div class="col-md-5" data-v-8ece5e29><img${ssrRenderAttr("src", __props.seo.sec_img)} alt="Web design" class="shadow-lg rounded align-self-center" loading="lazy" style="${ssrRenderStyle({ "width": "500px", "height": "400px", "object-fit": "cover" })}" data-v-8ece5e29></div><div class="col-md-7 align-self-center" data-v-8ece5e29><h2 class="fw-bold mb-3" data-v-8ece5e29> Reach the Right People, the Right Way</h2><p data-v-8ece5e29>We don’t just post and pray — we strategize. From SEO and social media to email campaigns and paid ads, we use data-driven tactics to grow your audience, increase engagement, and drive real business results. It&#39;s marketing with purpose and measurable impact. </p></div></div></div></section><section class="py-5 banner-image" data-v-8ece5e29><div class="container px-4" data-v-8ece5e29><div class="row" data-v-8ece5e29><div class="col-md-4 col-sm-6 mt-4 mb-4" data-v-8ece5e29><div class="p-3 border-0 text-white d-flex align-items-center justify-content-between gap-3 icon-box" data-v-8ece5e29><div data-v-8ece5e29><h3 class="fw-bold mb-1" data-v-8ece5e29>Organic SEO</h3><p class="mb-0" data-v-8ece5e29>Let us help you achieve top rankings on the first page of Google and other leading search engines.</p></div></div></div><div class="col-md-4 col-sm-6 mt-4 mb-4" data-v-8ece5e29><div class="p-3 border-0 text-white d-flex align-items-center justify-content-between gap-3 icon-box" data-v-8ece5e29><div data-v-8ece5e29><h3 class="fw-bold mb-1" data-v-8ece5e29>Google Ads</h3><p class="mb-0" data-v-8ece5e29>Expertly manage and implement all Google AdWords features for optimal performance.</p></div></div></div><div class="col-md-4 col-sm-6 mt-4 mb-4" data-v-8ece5e29><div class="p-3 border-0 text-white d-flex align-items-center justify-content-between gap-3 icon-box" data-v-8ece5e29><div data-v-8ece5e29><h3 class="fw-bold mb-1" data-v-8ece5e29>Social Media</h3><p class="mb-0" data-v-8ece5e29>Our dedicated team will oversee your brand’s presence across all major social media platforms and networks.</p></div></div></div><div class="col-md-4 col-sm-6 mt-4 mb-4" data-v-8ece5e29><div class="p-3 border-0 text-white d-flex align-items-center justify-content-between gap-3 icon-box" data-v-8ece5e29><div data-v-8ece5e29><h3 class="fw-bold mb-1" data-v-8ece5e29>Web Design</h3><p class="mb-0" data-v-8ece5e29>We specialize in creating professional, user-friendly websites and graphic designs tailored to enhance your business’s online presence.</p></div></div></div><div class="col-md-4 col-sm-6 mt-4 mb-4" data-v-8ece5e29><div class="p-3 border-0 text-white d-flex align-items-center justify-content-between gap-3 icon-box" data-v-8ece5e29><div data-v-8ece5e29><h3 class="fw-bold mb-1" data-v-8ece5e29>Brand Strategy</h3><p class="mb-0" data-v-8ece5e29>We strategically position your brand across various platforms, ensuring it stands out and outperforms your competitors.</p></div></div></div><div class="col-md-4 col-sm-6 mt-4 mb-4" data-v-8ece5e29><div class="p-3 border-0 text-white d-flex align-items-center justify-content-between gap-3 icon-box" data-v-8ece5e29><div data-v-8ece5e29><h3 class="fw-bold mb-1" data-v-8ece5e29>Auto Lead Generation</h3><p class="mb-0" data-v-8ece5e29>Our main goal is to ensure that you consistently receive a steady stream of leads and grow your business.</p></div></div></div></div></div></section>`);
      _push(ssrRenderComponent(Footer, { seo: __props.seo }, null, _parent));
      _push(`<!--]-->`);
    };
  }
};
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Services/DigitalMarketing.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const DigitalMarketing = /* @__PURE__ */ _export_sfc(_sfc_main, [["__scopeId", "data-v-8ece5e29"]]);
export {
  DigitalMarketing as default
};

import Vue from 'vue'
import VueRouter from 'vue-router'
import Home from '../components/Home'

import Bhome from '../components/shop/brands/Home.vue'
import Newly from '../components/shop/brands/newly.vue'
import Compile from '../components/shop/brands/compile.vue'
import Categories from '../components/shop/categories/home.vue'
import News from '../components/shop/categories/News.vue'
import Compiles from '../components/shop/categories/compiles.vue'
import Customers from '../components/shop/customers/home.vue'
import Products from '../components/shop/products/home.vue'
import ParNew from '../components/shop/products/new.vue'
import ParCompile from '../components/shop/products/compile.vue'
import Waste from '../components/shop/waste/home.vue'
import Meter from '../components/shop/meter/home.vue'
import Order from '../components/shop/order/home.vue'
import Details from '../components/shop/order/details.vue'
Vue.use(VueRouter)

const routes = [
    {
        path: '/',
        name: 'home',
        component: Home
    },

    {
        path: '/shop/brands',
        name: 'bhome',
        component: Bhome,
    },

    {
        path: '/shop/brands/newly',
        name: 'newly',
        component: Newly
    }
    ,
    {
        path: '/shop/brands/compile/:id',
        name: 'compile',
        component: Compile
    }
    /* ------------------------- 商品分类*/
    ,
    {
        path: '/shop/categories', // 对应路由路径
        name: 'categories',
        component: Categories
    },
    {
        path: '/shop/categories/news',
        name: 'news',
        component:News
    },
    {
        path: '/shop/categories/compiles/:id',
        name: 'compiles',
        component:Compiles
    },
    // 会员管理
    {
        path: '/shop/customers',
        name: 'customers',
        component: Customers
    },
    // 商品管理
    {
        path: '/shop/products',
        name: 'roducts',
        component: Products
    },
    {
        path: '/shop/products/parNew',
        name: 'parnew',
        component: ParNew
    },
    {
        path: '/shop/products/parcompile/:id',
        name: 'parcompile',
        component: ParCompile
    },
    {
        path: '/shop/waste',
        name:'waste',
        component:Waste
    },
    {
        path: '/shop/meter',
        name:'meter',
        component:Meter
    },
    {
        path: '/shop/order',
        name: 'order',
        component:Order
    },
    {
        path: '/shop/order/details/:id',
        name: 'details',
        component:Details
    }
];

const router = new VueRouter({
    routes
});

export default router;

<template>
    <div>
        <el-breadcrumb separator="/">
        <el-breadcrumb-item :to="{ path: '/' }">首页</el-breadcrumb-item>
        <el-breadcrumb-item :to="{ path: '/shop/products' }">商城管理</el-breadcrumb-item>
        <el-breadcrumb-item :to="{ path: '/shop/order' }">订单管理</el-breadcrumb-item>
         </el-breadcrumb>

         <!-- 卡片试图 -->
         <div class="box-card">
         <el-card  v-for="item in formlist" :key="item.id" :class="border[item.status-1]" body-style="{border-color='red'}" 
>
             <div class="trading">
                 <div>
                     <li><h3 :class="font[item.status-1]">{{item.status| statuss}}</h3></li>
                     <li ><span>{{item.created_at}}</span> | <span>解决<a href="#">(1231233)</a></span> | <span>订单号: <a href="#">{{item.id}}</a></span></li>
                 </div>
                 <div><span>订单金额 : {{item.total_price}}</span></div>
             </div>
             <div class="show" v-for="items in item.order_products" :key="items.id">
                 <div class="show_buttom">
                     <div><img :src="items.product.image ===null || items.product.image ==='' ? '':'http://images.canon4ever.com/'+items.product.image" alt=""></div>
                     <div><p>{{items.product.name}}</p></div>
                 </div>
                 <div class="show_button">
                     <el-button type="success"
                     @click="details(item.id)">查看详情</el-button>
                 </div>      
             </div>
         </el-card>
         </div>
         <!-- 分页 -->
         <el-pagination
        background
        layout="prev, pager, next"
        :total="page.total"
        :page-size="page.pageSize"
        @current-change="handleCurrentChange">
</el-pagination>
    </div>
</template>
<script>
export default {
    data(){
        return{
            // 接受数据
            formlist:[],
            // 页数
            page:{
                total:0,
                pageSize:0,
                current_page:1
            },
            // 订单状态颜色
            border:[
                'gen_paly', // 待支付
                'the_delivery', // 待发货
                'distribution', // 配货
                'the_goods', // 待收货
                'complete' // 完成
            ],
            // 标题颜色
            font:[
                'h1',
                'h2',
                'h3',
                'h4',
                'h5'
            ]
        }
    },
    // 页面加载之前挂载
    created(){
        this.info()
    },
    // 过滤器
    filters:{
        statuss(val){
            let status =['代付款','代发货','配货中','待收货','交易成功']
            return status[val-1]
        }
    },
    methods:{
        info(){
            axios.get(`http://localhost:8000/admin/shop/orders?page=${this.page.current_page}`)
            .then(res=>{
                console.log(res);
                let formlist = res.data.data.orders;
                this.formlist = formlist.data;
                console.log(this.formlist);
                // 请求 分页符
        this.page = {
            total:formlist.total,// 总页数
            pageSize:formlist.per_page,// 显示的条数
            current_page:formlist.current_page// 当前页
            }
            })
        },
         // 分页符
      handleCurrentChange(val){
          console.log(val);
          this.page.current_page = val
          this.info()
      },
      // 查看详情
      details(id){
          console.log(id);
          this.$router.push({ name: 'details',params:{id:id} }) //动态匹配路由
      }
    }
}
</script>
<style lang="scss" scoped>
*{
    margin: 0px;
    padding: 0px;
}
.box-card{
    margin-top: 20px
}
.el-card{
    margin-bottom: 20px;
}
.trading{
    display: flex;
    justify-content:space-between;
    border-bottom: 1px solid #ccc;
    span{
        line-height: 54px;
    }
    li {
        list-style: none;
        span{
            a{
                text-decoration: none;
                color: skyblue;
            }
        }
    }
    :nth-of-type(2){
        margin: 20px 0px 25px 0px
    }
}
.show{
    display: flex;
    justify-content:space-between;
    align-items: center;
    margin-top: 20px;
    .show_buttom{
        display: flex;
        align-items: center;
        img{
            width: 100px;
            height: 100px;
        }
    }
    .show_button .el-button{
        line-height:3;
        padding: 0px 18px;
    }
}

.complete{
     border: 1px solid green; // 完成
}
.gen_paly{
    border: 1px solid red; // 待支付
}
.the_goods{
    border: 1px solid orange; // 待收货
}
.the_delivery{
    border: 1px solid red; // 待发货
}
.distribution{
    border: 1px solid green; // 配送中
}
.h1{
    color: red;
}
.h2{
    color: red;
}
.h3{
    color: green;
}
.h4{
    color: orange;
}
.h5{
    color: #ccc;
}
</style>
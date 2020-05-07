<template>
    <div >
        <!-- 面包屑导航栏 -->
        <el-breadcrumb separator="/">
        <el-breadcrumb-item :to="{ path: '/' }">首页</el-breadcrumb-item>
        <el-breadcrumb-item :to="{ path: '/shop/products' }">商城管理</el-breadcrumb-item>
        <el-breadcrumb-item :to="{ path: '/shop/order' }">订单管理</el-breadcrumb-item>
        <el-breadcrumb-item :to="{ path: '/shop/order/details' }">订单详情</el-breadcrumb-item>
         </el-breadcrumb>
        <div class="font"><h3>订单号: </h3><span>{{formlist.id}}</span></div>

        <!-- 交易显示 -->
        <div class="state">
            <h2>{{formlist.status| statuss}}</h2>
            <!-- 配货显示 -->
            <div class="dis_buton">
                 <el-button type="primary" v-if="formlist.status == 2" class="distr_show"
                 @click="handleDis">配货</el-button>
            </div>
             
             <div v-if="formlist.status == 3 || formlist.status == 4" class="wuliu_show">
            <el-form :inline="true" >
                <!-- 物流信息 -->
                <el-form-item >
                         <el-select v-model="formlist.express_id" placeholder="请选择快递">
                    <el-option
                        v-for="item in expresses"
                        :key="item.id"
                        :label="item.name"
                        :value="item.id">
                    </el-option>
               </el-select>
                  </el-form-item>
               
              <!-- 物流单号 -->
              <el-form-item >
                   <el-input  v-model="formlist.express_code" placeholder="请输入快递单号"></el-input>
              </el-form-item>
           
               <!-- 按钮 -->
             <el-form-item >
                <el-button type="danger" @click="handelModify">{{formlist.status ==3?'发货':'修改订单'}}</el-button>
                 <el-button type="success" v-if="formlist.status == 4" class="complete"
                 @click="tradingComplete">交易成功</el-button>
              </el-form-item>
            </el-form>
            </div>
            
            <!-- 订单状态 -->
            <el-steps :space="200" :active="formlist.status" finish-status="success" align-center>
                <el-step title="下单" :description="formlist.created_at"></el-step>
                <el-step title="付款" :description="formlist.pay_time"></el-step>
                <el-step title="配货" :description="formlist.picking_time"></el-step>
                <el-step title="出库" :description="formlist.shipping_time"></el-step>
                <el-step title="交易成功" :description="formlist.finish_time"></el-step>
            </el-steps>
        </div>

        <!-- 物流查询 -->
       <el-collapse v-model="activeNames" v-if="formlist.status > 3" class="logistics_show">
        <el-collapse-item title="展开物流详情" name="2" >
            <iframe :src="'http://m.kuaidi100.com/index_all.html?type='+ formlist.express + '&postid='+formlist.express_code" width="100%" height="500px"></iframe>
        </el-collapse-item>
       </el-collapse>

       <!-- 购买的商品 -->
       <div v-for="item in formlist.order_products"
       :key="item.id">
       <div class="goods" >
           <div class="goods_image">
                <img :src="item.product.image ===null || item.product.image ==='' ? '':'http://images.canon4ever.com/'+item.product.image" alt="">
           </div>
           <div><p>{{item.product.name}}<span>{{item.product.price}}元x{{item.num}}</span></p></div>
       </div>

       <!-- 用户信息 -->
       <div class="information">
           <dl>
               <dt>收货信息</dt>
               <dd>客户编号: <span>{{formlist.customer_id}}</span></dd>
               <dd>姓&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;名: <span>{{formlist.address.name}}</span></dd>
               <dd>联系电话: <span>{{formlist.address.tel}}</span></dd>
               <dd>收货地址: <span>{{formlist.address.city}}</span>
               <span>{{formlist.address.area}}</span>
               <span>{{formlist.address.detail}}</span>
               </dd>
           </dl>
       </div>

       <!-- 支付方式 -->
       <div class="way">
            <dl>
               <dt>支付方式及送货时间</dt>
               <dd>支付方式: <span>货到付款</span></dd>
               <dd>送货时间: <span>不限送货时间</span></dd>
           </dl>
       </div>

       <!-- 总价 -->
       <div class="settlement">
           <dl>
               <dd><span>商品总价:</span><span class="monery">{{formlist.total_price}}</span></dd>
               <dd><span>运&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;费:</span><span class="monery">{{formlist.express_money}}</span></dd>
               <dd><span>订单金额:</span><span class="monery">{{sum}}</span></dd>
               <dd><span>实际金额:</span><span class="sum">{{sum}}</span></dd>
           </dl>
       </div>
       </div>
    </div>
</template>
<script>
export default {
    data(){
        return{
            // 接受请求到的数据
            formlist:{
                express:'',
                address:{}
            },
            activeNames: ['1'],
            // 存放物流选项框
            expresses:[]
        }
    },
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

        // 请求数据
        info(){
             // 接受当前的路由的id
        let id = this.$route.params.id;
       axios.get(`/admin/shop/orders/${id}`).then(res=>{
           let formlist = res.data.data.order;
           // 给私有数据传值
           this.formlist = formlist
            console.log(this.formlist);
        //    console.log(this.formlist.express.code,111);
            // 请求物流消息
            this.expresses = res.data.data.expresses
            console.log(this.expresses,1222);
            if(this.formlist.express_id==0){
                this.formlist.express_id=''
            }
            // code
            let code = this.formlist.express == null?'':this.formlist.express.code
            this.formlist.express = code
       })
        },
        // 配货
        handleDis(){
            axios.patch(`/admin/shop/orders/picking?id=${this.formlist.id}`)
            this.formlist.status = 3
            
        },
        // 修改或者发货
        handelModify(){
            axios.patch(`/admin/shop/orders/shipping?id=${this.formlist.id}&status=${this.formlist.status}
            &express_id=${this.formlist.express_id}&express_code=${this.formlist.express_code}`)
            this.info()
            if(this.status == 3 ){
                this.$notify.success('发货成功')
            }
                this.$notify.success('修改成功')
        },
        // 交易完成
        tradingComplete(){
            axios.patch(`/admin/shop/orders/finish?id=${this.formlist.id}`)
            this.info()
            this.$notify.success('提交成功')
       }
    },
    // 计算属性
    computed:{
        // 总余额
        sum(){
            return (parseFloat(this.formlist.total_price)+parseFloat(this.formlist.express_money))
        },
        // 发货/修改
        Modify(){
            formlist.status ==3?'发货':'修改订单'
        }
        
}
}

</script>
<style lang="scss" scoped>
.el-breadcrumb{
    margin-bottom: 20px;
}
.font{
    border-bottom:1px solid #ccc;
    padding: 4px 0px;
    h3{
        display: inline;
        margin-right: 20px;
    }
}
.state{
    position: relative;
    h2{
        margin-top: 20px;
    }
    .el-steps{
        margin-top: 100px;
        justify-content: center;
    }
    .dis_buton{
        button{
        position: absolute;
        top: 0px;
        right: 0px;
    }
    .wuliu_show{
        position: absolute;
        top: 0px;
        right: 0px;
    }
    }
    
    .inputWidth{
        width: 240px;
        height: 30px;
    }
    .wuliu_show{
        display: block;
        position: absolute;
        right: 0px;
    }
}
// //物流隐藏
// .logistics{
//     display: none;
// }
// 物流显示
.logistics_show{
    display: block;
}
// 配货显示
.distr_show{
    display: block;
}
.el-collapse{
    margin: 50px 0px 30px 0px;
}
// 交易完成
.complete{
    display: inline-block;
}
// 商品的展示
.goods{
    display: flex;
    align-items: center;
    border-bottom: 1px solid #ccc;
    padding: 10px 0px;
    margin:30px 0px 0px 20px;
    div{
        display: inline-block;
        margin-right: 20px;
        span{
           margin-left: 30px;
        }
    }
    .goods_image{
        width: 100px;
        height: 100px;
        img{
            width: 100%;
            height: 100%;
        }
    }
}

// 用户信息
.information{
    border-bottom: 1px solid #ccc;
    dl{
        font-size: 14px;
         dt{
        font-weight: bold;
        font-size: 18px;
        margin: 30px 0px 20px 0px;
    }
        dd{
            margin: 10px 0px;
        }
    }
   
}

// 支付方式
.way{
    border-bottom: 1px solid #ccc;
    padding: 0px 0px 10px 0px;
    margin-bottom: 10px;
    dl{
          font-size: 14px;
           dt{
        font-weight: bold;
        font-size: 18px;
        margin: 30px 0px 20px 0px;
    }
    dd{
            margin: 10px 0px;
        }
    }
}

// 结算
.settlement{
    dl{
        float: right;
        font-size: 14px;
        dd{
            margin: 10px 0px;
            span:nth-of-type(1){
                margin-right: 90px;
            }
            .monery{
                margin-left: 44px;
            }
            .sum{
                font-weight: 800;
                font-size: 30px;
                color: #C86B41;
            }
            span:nth-of-type(2){
                color: #C86B41;
            }
        }
        :nth-of-type(4){
            margin-top: 25px;
        }
        span{
            margin: 0px 15px;
            
        }
    }
}
.el-collapse-item{
    border: 1px solid green;
}
</style>
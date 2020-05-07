<template>
  <div>
    <!-- 面包屑导航 -->
    <el-breadcrumb separator="/">
      <el-breadcrumb-item :to="{ path: '/' }">首页</el-breadcrumb-item>
      <el-breadcrumb-item :to="{ path: '/shop/brands' }"
        >商城管理</el-breadcrumb-item>
      <el-breadcrumb-item>会员管理</el-breadcrumb-item>
    </el-breadcrumb>
    <!-- 搜索框 -->
    <el-form :inline="true" :model="coustomer" class="demo-form-inline">
  <el-form-item >
    <el-input v-model="coustomer.nickname" placeholder="名称"></el-input>
  </el-form-item>
  <el-form-item>
    <el-input v-model="coustomer.openid" placeholder="openid"></el-input>
  </el-form-item>
   <el-form-item>
    <el-select v-model="coustomer.sex" placeholder="性别">
      <el-option label="男" value="1"></el-option>
      <el-option label="女" value="2"></el-option>
    </el-select>
  </el-form-item>
     <!-- 日期 -->
     <el-form-item>
    <el-date-picker
      v-model="coustomer.created_at"
      type="datetimerange"
      range-separator="至"
      start-placeholder="开始日期"
      end-placeholder="结束日期"
      value-format="yyyy-MM-dd"
      unlink-panels
        >
    </el-date-picker>
     </el-form-item>
     <el-button type="primary" @click="onSubmit">按钮</el-button>
    </el-form>
    <!-- 表格 -->
    <el-table
      :data="tableData"
      style="width: 100%">
      <el-table-column type=index width="180"></el-table-column>
      <el-table-column label="缩略图" width="180">
          <template slot-scope="scope" >
              <img :src="scope.row.headimgurl" alt="" width="50px">
          </template>
      </el-table-column>
      <el-table-column prop="nickname" label="名称" width="180"></el-table-column>
      <el-table-column prop="openid" label="openid" width="180"></el-table-column>
      <el-table-column prop="sex" label="性别" width="180">
          <template slot-scope="scope">
              <span>{{ scope.row.sex | tableSex}}</span>
          </template>
      </el-table-column>
      <el-table-column  label="关注时间" width="180">
          <template slot-scope="scope">
          <span>{{scope.row.created_at | creTiem }}</span>
          </template>
      </el-table-column>
      </el-table>
      <el-pagination
        background
        layout="prev, pager, next"
        :total="page.total"
        :page-size="page.pageSize"
        @current-change="handleCurrentChange"
  >
</el-pagination>
  </div>
</template>
<script>
  export default {
    data() {
      return {
        formInline: {
          user: '',
          region: ''
        },
         value1: '',
        value2: '',
        // 请求的到的列表数据
        tableData: [],
        // 搜索的三个框
        coustomer:{
            sex:'',
            created_at:'',
            openid:'',
            nickname:''
        },
        // 页数
        page:{
            total:0,
            pageSize:0,
            current_page:1
        }
      }
    },
    // 过滤器
    filters:{
        // 过滤时间
        creTiem(val){
           let time = val;
           // 截取时间的前缀,然后返回出去
           let times = time.split(' ');
         return val = times[0]
        },
        // 过滤 1= 男, 2= 女
        tableSex(val){
            if (val == 1) {
                return val = '男'
            }else{
                return val = '女'
            }
        }
    },
    // 页面开始渲染数据
  created: function() { 
      this.info()
  }, 
    methods: {
        info(){
      // 搜索功能请求参数
      axios.get(`http://localhost:8000/admin/shop/customers?page=${this.page.current_page}
      &nickname=${this.coustomer.nickname}
      &openid=${this.coustomer.openid}
      &sex=${this.coustomer.sex}
      &created_at=${this.coustomer.created_at}`)
      // 请求数据库数据
      .then((res) => {
      // console.log(res)
      let tableData = res.data.data.customers
      console.log(tableData, 12)
      this.tableData = tableData.data;
      // 请求 分页符
      this.page = {
            total:tableData.total,
            pageSize: tableData.per_page,
            current_page:tableData.current_page
      }
    });
},
      // 查询按钮
      onSubmit() {
          this.info()
          console.log(this.coustomer,123);
          
      },
      // 分页符
      handleCurrentChange(val){
          this.page.current_page = val
          this.info()
      }
    }
  }
</script>
<style lang="scss" scoped>
.el-form-item .el-form-item__content{
   widows: 60px;
}
.el-form--inline .el-form-item{
  margin-right: 0px;
}
.el-main .demo-form-inlineb{
  margin: 15px 0px;
}
</style>

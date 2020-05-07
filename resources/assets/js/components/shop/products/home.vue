<template>
    <div>
      <!-- 搜索框 -->
    <el-form :inline="true" :model="coustomer" class="demo-form-inline">
    <el-col :span="3">
  <el-form-item >
    <el-input v-model="coustomer.name" placeholder="商品名称"></el-input>
  </el-form-item>
    </el-col>
    <el-col :span="3">
   <el-form-item :data="one">
       <!-- 商品分类 -->
        <el-select v-model="coustomer.category_id" placeholder="商品分类">
    <el-option-group
      v-for="item in one"
      :key="item.id"
      :label="item.name">
      <el-option
        v-for="items in item.children"
        :key="items.id"
        :label="items.name"
        :value="items.id">
      </el-option>
    </el-option-group>
  </el-select>
  </el-form-item>
    </el-col>
  <!-- 多选框 -->
  <el-col :span="5.5">
    <el-checkbox v-model="coustomer.is_top">置顶</el-checkbox>
    <el-checkbox v-model="coustomer.is_recommend">推荐</el-checkbox>
    <el-checkbox v-model="coustomer.is_hot">热销</el-checkbox>
    <el-checkbox v-model="coustomer.is_new">新品</el-checkbox>
  </el-col>
  <el-col :span="3">
      <el-form-item>
    <el-select v-model="coustomer.is_onsale" placeholder="上价状态">
      <el-option label="上架" value="1"></el-option>
      <el-option label="下架" value="2"></el-option>
    </el-select>
  </el-form-item>
  </el-col>
     <!-- 日期 -->
     <el-form-item>
          <el-col :span="3">
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
    </el-col>
     </el-form-item>
     <el-button type="primary" @click="onSubmit">查询</el-button>
     <el-button type="success" icon="el-icon-edit" @click="onSubmit">
       <router-link :to="{name:'parnew'}">新增</router-link>
     </el-button>
    </el-form>

     <!-- 表格 -->
    <el-table
      :data="tableData"
      style="width: 100%">
      <el-table-column prop="id" label="id" width="50"></el-table-column>
      <el-table-column prop="image" label="缩略图" width="100">
          <template slot-scope="scope" >
              <img :src="scope.row.image===null || scope.row.image===''?'':'http://images.canon4ever.com/'+scope.row.image" alt="" width="50px">
          </template>
      </el-table-column>
      <el-table-column prop="name" label="商品名称" width="100"></el-table-column>
      <el-table-column  label="所属分类" width="100">
          <template slot-scope="scope"> 
              <!-- 过滤分类 -->
              {{scope.row.categories | join_categories}}
          </template>
      </el-table-column>
      <el-table-column  label="品牌" width="50"></el-table-column>
      <el-table-column prop="price" label="单价" width="100"></el-table-column>
      <el-table-column prop="is_onsale" label="上架" width="50">
          <template slot-scope="scope">
           <i :class="scope.row.is_onsale | changeObudr"></i>
        </template>
      </el-table-column>
      <el-table-column prop="is_top" label="置顶" width="50">
          <template slot-scope="scope">
           <i :class="scope.row.is_top | changeObudr"></i>
        </template>
      </el-table-column>
      <el-table-column prop="is_recommend" label="推荐" width="50">
          <template slot-scope="scope">
           <i :class="scope.row.is_recommend | changeObudr"></i>
        </template>
      </el-table-column>
      <el-table-column prop="is_hot" label="热销" width="50">
          <template slot-scope="scope">
           <i :class="scope.row.is_hot | changeObudr"></i>
        </template>
      </el-table-column>
      <el-table-column prop="is_new" label="新品" width="50">
          <template slot-scope="scope">
           <i :class="scope.row.is_new | changeObudr"></i>
        </template>
      </el-table-column>
      <el-table-column prop="stock" label="库存" width="100">
          <template slot-scope="scope">
          <el-input type="text" v-model="scope.row.stock"></el-input>
          <!-- 排序 -->
        </template>
      </el-table-column>
      <el-table-column  label="上映日期" width="100">
          <template slot-scope="scope">
          <span>{{scope.row.created_at | creTiem }}</span>
          </template>
      </el-table-column>
      <el-table-column label="操作" width="180">
          <template slot-scope="scope">
              <el-button size="mini" 
              @click="handleEdit(scope.$index, scope.row)">编辑</el-button>
              <el-button size="mini" type="danger" 
              @click="handleDelete(scope.$index, scope.row)">删除</el-button>
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
          one:[],
         value1: '',
        value2: '',
        // 请求的到的列表数据
        tableData: [],
        // 搜索的三个框
        coustomer:{
            name:'',
            category_id:'',
            brand_id:'',
            created_at:'',
            is_hot:'',
            is_top:'',
            is_recommend:'',
            is_onsale:'',
            is_new:'',
        },
        // 页数
        page:{
            total:0,
            pageSize:0,
            current_page:1
        },
        // oneData: [],
        value: ''
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
        },
        // 过滤字体图标
        changeObudr(value){
      return value ?'el-icon-check' : 'el-icon-close'
    },
    // 过滤分类:
    join_categories(value){
        let categories = value.map(item=>{
            return item.name
        })
        return categories.join(',')
    }
    },
    // 页面开始渲染数据
  created: function() { 
      this.info(),
       // 二次请求
      axios.get(`http://localhost:8000/admin/shop/categories`).then((res) => {
      console.log(res)
      let oneData = res.data.data.categories
      
      this.oneData = oneData
      this.one = oneData
      console.log(oneData,'xxx');
    // const onemap = oneData.map(item=>{
    //    return item.name
    // })
    // this.oneData.label = onemap
    //   console.log(this.oneData.label);
      
    });
  }, 
    methods: {
        info(){
      // 搜索功能请求参数
      axios.get(`http://localhost:8000/admin/shop/products?page=${this.page.current_page}
      &name=${this.coustomer.name}
      &category_id=${this.coustomer.category_id}
      &created_at=${this.coustomer.created_at}
      &is_hot=${this.coustomer.is_hot}
      &is_new=${this.coustomer.is_new}
      &is_onsale=${this.coustomer.is_onsale}
      &is_recommend=${this.coustomer.is_recommend}
      &is_top=${this.coustomer.is_top}`)
      // 请求数据库数据
      .then((res) => {
      console.log(res,11)
      let tableData = res.data.data.products
      console.log(tableData, 12)
      this.tableData = tableData.data;
      // 请求 分页符
      this.page = {
            total:tableData.total,// 总页数
            pageSize:tableData.per_page,// 显示的条数
            current_page:tableData.current_page// 当前页
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
          console.log(val);
          this.page.current_page = val
          this.info()
      },
      // 编辑页面, 传参
    handleEdit(index, row) {
      console.log(index, row,111)
       this.$router.push({ name: 'parcompile', params: { id: row.id } }) //动态匹配路由
    },
    //删除按钮
    handleDelete(index, row) {
      //  this.brand.splice(index,1)//静态删除
      this.$confirm('此操作将永久删除该文件, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning',
        center: true,
      })
        .then(() => {
          axios
            .delete(`http://localhost:8000/admin/shop/products/${row.id}`)
            .then((res) => {
              this.$message({
                type: 'success',
                message: '删除成功!',
              })
              // 把删除的时间赋值给当前的删除的元素
              this.tableData.deleted_at = this.tableData.updated_at
              this.info()
            })
        })
        .catch(() => {
          this.$message({
            type: 'info',
            message: '已取消删除',
          })
        })
    }
    }
  }
</script>
<style lang="scss" scoped>
.cell .el-icon-check{
  color: green;
}
.cell .el-icon-close{
  color: red;
}
</style>
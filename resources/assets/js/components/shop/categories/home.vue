<!-- 分类 -->
<template>
  <div>
    <el-row>
      <el-button type="success">
        <i class="el-icon-edit"></i>
        <router-link :to="{ name: 'news'}">新增</router-link>
      </el-button>
      <el-button type="success" @click="unfold">
        <i class="el-icon-d-caret"></i>
        {{icon}}<!-- 展开按钮 -->
      </el-button
      >
    </el-row>

    <!-- 外层 -->
    <el-table :data="tableData" 
    row-key="id"
    v-if="changing"
    :default-expand-all="flag"
    style="width: 100%"
    >
     <el-table-column label="商品 ID" prop="id"> </el-table-column>
      <el-table-column label="缩略图">
        <template slot-scope="scope">
          <img :src="scope.row.image===null || scope.row.image===''?'':'http://images.canon4ever.com/'+scope.row.image" alt="" width="50px"/>
        </template>
      </el-table-column>
      <el-table-column label="品牌分类" prop="name"> </el-table-column>
      <el-table-column label="是否显示" prop="is_show"> 
        <template slot-scope="scope">
           <i :class="scope.row.is_show | changeObudr"></i>
        </template>
      </el-table-column>
      <el-table-column label="排序">
        <template slot-scope="scope">
          <el-input type="text" v-model="scope.row.sort_order"
          @change="sort(scope.row.id,scope.row.sort_order)"></el-input>
          <!-- 排序 -->
        </template>
      </el-table-column>
      <el-table-column label="描述" prop="description"> </el-table-column>
      <el-table-column label="操作">
        <template slot-scope="scope">
          <el-button size="mini" @click="handleEdit(scope.$index, scope.row)"
          >
            编辑
          </el-button>
          <el-button
            size="mini"
            type="danger"
            @click="handleDelete(scope.$index, scope.row)"
            >删除</el-button
          >
        </template> 
      </el-table-column>
    </el-table>
  </div>
</template>

<script>
export default {
  data() {
    return {
      tableData: [],
      flag:false,
      changing:true,
      show:true,
      icon:'展开',
      // band:{
      //   id:idd,
      //   sort_order:sort_order_id
      // }
    }
  },
  filters:{// 过滤器
    changeObudr(value){
      return value ?'el-icon-check' : 'el-icon-close'
    }
  },
  // 页面开始渲染数据
  created: function() {
    axios.get(`http://localhost:8000/admin/shop/categories`).then((res) => {
      console.log(res)
      let tableData = res.data.data.categories
      console.log(tableData, 12)
      this. tableData = tableData
    });
  },  
  methods: {
    // 页面更新
    gengxin(){
        axios.get(`http://localhost:8000/admin/shop/categories`).then((res) => {
      // console.log(res)
      let tableData = res.data.data.categories
      console.log(tableData, 12)
      this. tableData = tableData
    });
    },
    // 编辑页面
    handleEdit(index, row) {
      console.log(index, row,111)
       this.$router.push({ name: 'compiles', params: { id: row.id } }) //动态匹配路由
    },
    /* 
    排 序
    */
   sort(id,sort_order){
     // 由于请求的是接受到的值,所有不能在data上定义参数
      axios.patch('http://localhost:8000/admin/shop/categories/sort_order',{id:id,sort_order:sort_order})
     .then (res=>{
        // console.log(res,1222);
        // 把res里面的数据重新赋值给页面,===>进行刷新
        this.tableData = res.data.data.categories
      })
      .catch(error=>{
      console.log(error,666);
        // 如果后台响应的结果和 请求的结果等于422就给个提示错误
       if (error.respinse && error.res.status == '422') {
          this.$message.error('请输入小于99的或者正确的');
          console.log('错误');
       }
      });
    },
    // 删除文件 
    handleDelete(index, row) {
       //  this.brand.splice(index,1)//静态删除
      this.$confirm('此操作将永久删除该文件, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning',
        center: true,
      })
        .then(() => {
          axios.delete(`http://localhost:8000/admin/shop/categories/${row.id}`)
            .then((res) => {
              this.$message({
                type: 'success',
                message: '删除成功!',
              })
               this.gengxin()
            })
        })
        .catch(() => {
          this.$message({
            type: 'info',
            message: '已取消删除',
          })
        })
    },
    unfold(){
      this.flag = !this.flag
      this.changing = false;
      this.show = !this.show // 关闭按钮切换
      this.show ==true ? this.icon = '展开':this.icon = '关闭'
      this.$nextTick(()=>{// dom更新结束之后执行延迟回调
        this.changing = true
      })
    }
  },
}
</script>
<style>
.demo-table-expand {
  font-size: 0;
}
.demo-table-expand label {
  width: 90px;
  color: #99a9bf;
}
.demo-table-expand .el-form-item {
  margin-right: 0;
  margin-bottom: 0;
  width: 50%;
}
/* .el-table__expand-icon>.el-icon{
z-index: 10000;
left: 150%;
  } */

.el-table__expanded-cell[class*='cell'] {
  padding: 15px 0px !important;
}
.cell .el-icon-check{
  color: green;
}
.cell .el-icon-close{
  color: red;
}
.el-input__inner{
  width: 60%;
}
</style>

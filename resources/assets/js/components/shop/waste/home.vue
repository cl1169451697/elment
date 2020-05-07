<template>
    <div>
        <el-breadcrumb separator="/">
        <el-breadcrumb-item :to="{ path: '/' }">首页</el-breadcrumb-item>
        <el-breadcrumb-item :to="{ path: '/shop/products' }">商城管理</el-breadcrumb-item>
        <el-breadcrumb-item :to="{ path: '/shop/waste' }">商品回收站</el-breadcrumb-item>
         </el-breadcrumb>
         <!-- 按钮 -->
         <el-row>
      <el-button type="success" 
      @click="multrestores">
        <i class="el-icon-edit"></i>
        还原
      </el-button>
      <el-button type="success" @click="multdeletes" >
        <i class="el-icon-d-caret"></i>
        删除
      </el-button
      >
    </el-row>

        <!-- 外层 -->
    <el-table 
    row-key="id"
    style="width: 100%"
    :data="brand"
    @selection-change="handleSeleionChange"
    >
    <el-table-column label="ID" prop="id" type="selection" > </el-table-column>
     <el-table-column label="ID" prop="id"> </el-table-column>
     <el-table-column label="商品名称" prop="name"> </el-table-column>
     <el-table-column label="缩略图" prop="image"> 
         <template slot-scope="scope">
             <img :src="scope.row.image===null || scope.row.image===''?'':'http://images.canon4ever.com/'+scope.row.image" alt="" width="50px"/>
         </template>
     </el-table-column>
     <el-table-column label="所属分类" prop="category_id"> </el-table-column>
     <el-table-column label="品牌" prop="categories"> </el-table-column>
     <el-table-column label="单价" prop="price"> </el-table-column>
     <el-table-column label="上架日期" prop="created_at"> 
         <template slot-scope="scope">
             <span> {{scope.row.created_at | creTiem}}</span>
         </template>
     </el-table-column>
     <el-table-column label="操作" > 
         <template slot-scope="scope">
          <el-button size="mini" @click="restores(scope.$index, scope.row)">
            还原
          </el-button>
          <el-button
            size="mini"
            type="danger"
            @click="deletes(scope.$index, scope.row)"
            >删除</el-button
          >
        </template>
     </el-table-column>
    </el-table>

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
            checked:true,
         brand: [],
         // 页码
         page: {
        total: 0,
        pageSize: 0,
        current_page: 1,
      },
      // 存储复选框的选中的id
      checked_id:[]
        }
    },
 created(){
    this.info()
    
    },
    // 过滤器
    filters:{
        // 过滤日期
        creTiem(val){
            // console.log(val);
            let times =  val.split(' ')
            return val = times[0]
        }
    },
    methods:{
        info(){
             axios.get(`http://localhost:8000/admin/shop/products/trash?page=${this.page.current_page}`)
            .then(res=>{
                console.log(res);
            let brand = res.data.data.products
            this.brand = brand.data
            // 分页符
            this.page={
            total:brand.total,// 总页数
            pageSize:brand.per_page,// 显示的条数
            current_page:brand.current_page// 当前页
            }
     })
        },
        handleCurrentChange: function(val) {
      console.log(val)
      this.page.current_page = val
      this.info()
    },
        // 行内还原
        restores(index,row){
            console.log(row.id);
            axios.get(`http://localhost:8000/admin/shop/products/${row.id}/restore`)
            .then(res=>{
                console.log(res);
                if(res.status !== 200){
                    this.$message.error('还原失败')
                }
                // 还原当前的数据
                this.brand.deleted_at = null;
                // 提示信息
                this.$message.success('恭喜你还原成功')
                this.info()
            })
        },
        // 行内删除
        deletes(index,row){
             this.$confirm('此操作将永久删除该文件, 是否继续?', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning',
                center: true,
            })
            .then(()=>{
                axios.delete(`http://localhost:8000/admin/shop/products/${row.id}/force_destroy`)
            .then(res=>{
            this.$message.success('删除成功')
            })
            this.info()
            })
            .catch(()=>{
                this.$message.error('删除失败')
            })
        },  
        // 多选框
        handleSeleionChange(val){
            // 把点击传中的复选框中的数组进行遍历, 取里面的id,然后传到 chcked_id数组里面
            this.checked_id = val.map(item=>{
                return item.id
            })
            
        },
       // 多选还原  
        multrestores(){
            if(this.checked_id.length == 0){
              return  this.$message.error('请至少选着一个记录')
            }
            axios.post(`http://localhost:8000/admin/shop/products/restore_checked`,{checked_id:this.checked_id})
            .then(res=>{
                this.info()
                this.$message.success('还原成功')
            })
        },
        // 多选删除
        multdeletes(){
            if(this.checked_id.length == 0){
              return  this.$message.error('请至少选着一个记录')
            }
            axios.post(`http://localhost:8000/admin/shop/products/force_destroy_checked`,{checked_id:this.checked_id})
            .then(res=>{
                this.info()
                this.$message.success('删除成功')
            })
        }
    }
}
</script>
<style lang="scss" scoped>
.el-row{
    margin-top: 25px;
}
</style>
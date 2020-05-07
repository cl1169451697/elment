<template>
  <el-main>
    <el-row>
      <el-button type="success">
        <i class="el-icon-edit"></i>
        <router-link :to="{ path: '/shop/brands/newly' }">新增</router-link>
      </el-button>
      <el-button type="success">
        <i class="el-icon-edit"></i>
        导出</el-button
      >
    </el-row>
    <el-table :data="brand">
      <el-table-column prop="id" label="品牌id"> </el-table-column>
      <el-table-column prop="img" label="缩略图">
        <template slot-scope="scope">
          <img :src="scope.row.image" alt="" width="50px" />
        </template>
      </el-table-column>
      <el-table-column prop="name" label="品牌名称"> </el-table-column>
      <el-table-column prop="url" label="品牌网站"> </el-table-column>
  <el-table-column
        prop="description"
        label="描述信息"
        show-overflow-tooltip
      >
      </el-table-column>
      <el-table-column prop="sort_order" label="排序"> </el-table-column>
      <el-table-column prop="is_show" label="是否显示">
        <template slot-scope="scope">
          <!-- <i :class="scope.row.is_show?'el-icon-check':'el-icon-close'"></i> -->
          <i :class="scope.row.is_show | changeObudr"></i>
        </template>
      </el-table-column>
      <el-table-column prop="operation" label="操作">
        <template slot-scope="scope">
          <el-button size="mini" @click="handleEdit(scope.$index, scope.row)">
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
    <el-pagination
      background
      layout="prev, pager, next"
      :total="page.total"
      :page-size="page.pageSize"
      @current-change="handleCurrentChange"
    >
    </el-pagination>
  </el-main>
</template>

<script>
import axios from 'axios'

export default {
  data() {
    return {
      brand: [],
      page: {
        total: 0,
        pageSize: 0,
        current_page: 1,
      },
    }
  },
  filters: {
    changeObudr: function(value) {
      return value ? 'el-icon-check' : 'el-icon-close'
    },
  },
  created: function() {
    // 页面一加载就更新
    this.info()
  },
  methods: {
    // 页面请求axix
    info: function() {
      axios
        .get(
          `http://localhost:8000/admin/shop/brands?page=${this.page.current_page}`
        )
        .then((respone) => {
          console.log(respone)
          let brand = respone.data.data.brands
          this.brand = brand.data
          this.page = {
            total: brand.total,
            pageSize: brand.per_page,
            current_page: brand.current_page,
          }
          console.log(this.page)
        })
    },
    delete: function() {
      // 分页的页数
      axios
        .get(
          `http://localhost:8000/admin/shop/brands?page=${this.page.current_page}`
        )
        .then((respone) => {
          console.log(respone)
          let rowleng = respone.data.data.brands.data.length
          console.log(rowleng)
          if (rowleng == 0 && this.page.current_page > 1) {
            //如果 长度为0,并且页数大于1
            this.page.current_page -= 1 // 就让页数
            this.info()
          } else {
            this.info()
          }
        })
    },
    handleCurrentChange: function(val) {
      console.log(val)
      this.page.current_page = val
      this.info()
    },
    // 编辑页面
    handleEdit(index, row) {
      console.log(index, row)
      this.$router.push({ name: 'compile', params: { id: row.id } }) //动态匹配路由
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
            .delete(`http://localhost:8000/admin/shop/brands/${row.id}`)
            .then((res) => {
              this.$message({
                type: 'success',
                message: '删除成功!',
              })
              this.delete()
            })
        })
        .catch(() => {
          this.$message({
            type: 'info',
            message: '已取消删除',
          })
        })
    },
  },
}
</script>
<style scoped>
a {
  color: #fff;
  text-decoration: none;
}
.el-icon-check {
  color: green;
  font-weight: bold;
}
.el-icon-close {
  color: red;
  font-weight: bold;
}
.back {
  color: #000;
}
</style>

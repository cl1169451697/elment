<!--  -->
<template>
  <el-row class="tac">
    <el-menu
      default-active="2"
      class="el-menu-vertical-demo"
      @open="handleOpen"
      @close="handleClose"
      :router="true"
      background-color="#545c64"
      text-color="#fff"
      active-text-color="#ffd04b"
    >
      <el-menu-item index="1">
        <i class="el-icon-menu"></i>
        <span slot="title">首 页</span>
      </el-menu-item>
      <el-submenu index="/shop">
        <template slot="title">
          <i class="el-icon-location"></i>
          <span>商城管理</span>
        </template>
        <el-menu-item-group>
          <el-menu-item index="/shop/meter">
            <i class="el-icon-rank"></i>
            仪表盘</el-menu-item
          >
          <el-menu-item index="/shop/order">
            <i class="el-icon-rank"></i>
            订单管理</el-menu-item
          >
          <el-menu-item index="/shop/products">
            <i class="el-icon-edit"></i>
            商品管理</el-menu-item
          >
          <el-menu-item index="/shop/categories">
            <i class="el-icon-more"></i>
            商品分类</el-menu-item
          >
          <el-menu-item index="/shop/brands">
            <i class="el-icon-view"></i>
            商品品牌</el-menu-item
          >
          <el-menu-item index="/shop/customers">
            <i class="el-icon-mobile-phone"></i>
            会员管理</el-menu-item
          >
          <el-menu-item index="/shop/">
            <i class="el-icon-set-up"></i>
            物流管理</el-menu-item
          >
          <el-menu-item index="/shop/waste">
            <i class="el-icon-delete"></i>
            商品回收站</el-menu-item
          >
        </el-menu-item-group>
      </el-submenu>
      
      
    
    </el-menu>
  </el-row>
</template>

<script>

  export default {
    methods: {
      handleOpen(key, keyPath) {
        console.log(key, keyPath);
      },
      handleClose(key, keyPath) {
        console.log(key, keyPath);
      }
    }
  }
</script>
<style scoped>
/* @import url(); 引入css类 */
</style>

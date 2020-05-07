<template>
	<div id="app">
		<el-container>
			  <el-header>
			<addnav></addnav>
				</el-header>
			<el-container>
				<!-- 左侧栏 -->
				<el-aside  class="tac">
				<!-- 挂载hande组件 -->
				<hande></hande>
				</el-aside>

				<!-- //中间区域 -->
				<el-main>
					<router-view/>
					
				</el-main>
			</el-container>


		</el-container>
	</div>

</template>

<script>
// 注册左侧组件
import Hande from "../components/shared/hande"
import Addnav from "../components/shared/nav"
    export default {
        data() {
            return {
                activeIndex: '1',
                activeIndex2: '1'
            };
		},
		components:{
			hande:Hande,
			addnav: Addnav
		},
        methods: {
            handleSelect(key, keyPath) {
                console.log(key, keyPath);
			}
        }
    }
</script>

<style>
* {
  margin: 0px;
  padding: 0px;
}
html,body,#app,.home,.el-container {
  height: 100%;
}
.el-aside{
  background-color: #545C64;
}
.el-menu{
border-right: none;
}
::-webkit-scrollbar {
        display: none;
    }
.el-header {
  margin: 0px;
  padding: 0px;
}
.is-vertical .el-menu-demo .el-menu-item{
color: #fff;
}
.el-menu-demo li{
  border:none!important;
  text-decoration: none;
color: #fff!important;
}
.el-menu-demo li:nth-of-type(1) {
    margin-left: 25px!important;
    font-size: 22px;
}
.el-menu-demo li a{
  text-decoration: none;
   color: #fff;
}
.el-menu-demo li:nth-of-type(2) {
  float: right;
}
.el-menu-vertical-demo li {
  text-align: center;
}
.el-aside {
  width: 200px !important;
}
.el-pagination{
  text-align: center;
  margin-top: 50px;
}
.has-gutter th{
  text-align: center;
}
.el-table__row td{
  text-align: center;
}
.el-table__header th{
text-align: center;
}
</style>

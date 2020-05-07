<!--  -->
<template>
<div>
  <el-breadcrumb separator="/">
  <el-breadcrumb-item :to="{ path: '/' }">首页</el-breadcrumb-item>
  <el-breadcrumb-item :to="{ path: '/shop/brands' }">商城管理</el-breadcrumb-item>
  <el-breadcrumb-item :to="{ path: '/shop/products' }">商品品牌</el-breadcrumb-item>
  <el-breadcrumb-item>编辑</el-breadcrumb-item>
  </el-breadcrumb>
  <h4>{{bianji}}</h4>
  <div>
      <el-form :model="ruleForm" :rules="rules" ref="ruleForm" label-width="100px" class="demo-ruleForm">
            
          <el-tabs value="first">
          <!-- 导航栏一 -->
        <el-tab-pane label="通用信息" name="first">
            
            <!-- 商品名称 -->
          <el-form-item label="商品名称" prop="name">
            <el-input v-model="ruleForm.name"></el-input>
          </el-form-item>

            <!-- 商品分类 -->
          <el-form-item prop="category_id">
            <el-select v-model="ruleForm.category_id" multiple  placeholder="商品分类">
            <el-option-group
              v-for="item in options"
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

            <!-- 单价 -->
            <el-form-item label="单价" prop="price">
                 <el-input-number v-model="ruleForm.price"  label="描述文字"></el-input-number>
            </el-form-item>

            <!-- 缩略图 -->
         <el-form-item label="缩略图">
          <el-upload
            class="upload-demo"
            drag
            action="/admin/photo"
            multiple
            :before-upload="beforeAvatarUpload"
            :file-list="fileList"
            list-type="picture"
            :limit="1"
            :on-exceed='jingao'
            :on-preview="handlePictureCardPreview"
            :on-remove="handleRemove"
            :on-success="handlePictureCardSuccess"
          >
            <i class="el-icon-upload"></i>
            <div class="el-upload__text">
              将文件拖到此处，或<em>点击上传</em>
            </div>
            <div class="el-upload__tip" slot="tip">
              只能上传jpg/png文件，且不超过500kb
            </div>
          </el-upload>
         </el-form-item>

           <!-- 点击方法的模态框 -->
          <el-dialog :visible.sync="dialogVisible">
            <img width="100%" :src="dialogImageUrl" alt="" />
          </el-dialog>

            <!-- 商品描述 -->
         <el-form-item label="品牌描述" prop="description">
            <el-input type="textarea" v-model="ruleForm.description"></el-input>
         </el-form-item>

         <el-form-item label="是否上架" prop="is_onsale">
            <el-switch v-model="ruleForm.is_onsale"></el-switch>
         </el-form-item>

         <!-- 加入推荐 -->
          <el-form-item label="加入推荐">
              <el-checkbox v-model="ruleForm.is_top">置顶</el-checkbox>
              <el-checkbox v-model="ruleForm.is_recommend">推荐</el-checkbox>
              <el-checkbox v-model="ruleForm.is_hot">热销</el-checkbox>
              <el-checkbox v-model="ruleForm.is_new">新品</el-checkbox>
          </el-form-item>
        </el-tab-pane>
         

         <!-- 导航栏二 -->
        <el-tab-pane label="商品介绍" name="third">
          <el-form-item label="商品介绍" prop="content">
            <!-- 新增编辑信息页 -->
           <quill-editor v-model="ruleForm.content" :options="editorOption" class="one"></quill-editor>
          </el-form-item>
        </el-tab-pane>

        <!-- 导航栏三 -->
        <el-tab-pane label="商品相册" name="second">
              <!-- 缩略图 -->
          <el-form-item label="缩略图">
            <el-upload
              class="upload-demo"
              drag
              action="/admin/photo"
              multiple
              :before-upload="beforeAvatarUpload"
              :file-list="fileList1"
              list-type="picture"
              :limit="4"
              :on-exceed="jingao"
              :on-preview="handlePictureCardPreview1"
              :on-remove="handleRemove1"
              :on-success="handlePictureCardSuccess1" 
            >
              <i class="el-icon-upload"></i>
              <div class="el-upload__text">
                将文件拖到此处，或<em>点击上传</em>
              </div>
              <div class="el-upload__tip" slot="tip">
                只能上传jpg/png文件，且不超过500kb
              </div>
            </el-upload>
          </el-form-item>
           <!-- 点击方法的模态框 -->
          <el-dialog :visible.sync="dialogVisible1">
            <img width="100%" :src="dialogImageUrl1" alt="" />
          </el-dialog>
        </el-tab-pane>
        </el-tabs>

        <!-- 编辑提交按钮 -->
     <el-form-item>
    <el-button type="primary" @click="submitForm('ruleForm')">提交</el-button>
    <el-button @click="resetForm('ruleForm')">重置</el-button>
  </el-form-item>
        </el-form>
  </div>
</div>
</template>

<script>
import 'quill/dist/quill.core.css'
import 'quill/dist/quill.snow.css'
import 'quill/dist/quill.bubble.css'

import { quillEditor } from 'vue-quill-editor'
  export default {
     components: {
    quillEditor
  }, 
    data() {
      return {
        fileList: [],
        fileList1:[],
        options:[],// 存放一级的数据
      dialogImageUrl: '',
      dialogVisible: false,
      dialogImageUrl1: '',
      dialogVisible1: false,
       num: '',
        ruleForm:{}, // 私有数据
          bianji:'编辑',
          img:'http://images.canon4ever.com/', // 缩略图显示要加 网络前缀
          // 新增编辑信息页
      editorOption:{
        placeholder:'请填写商品介绍信息...'
      },
        rules: {
          name: [
            { required: true, message: '请输入活动名称', trigger: 'blur' },
            { min: 3, max: 30, message: '长度在 3 到 5 个字符', trigger: 'blur' }
          ],
          url: [
            { required: true, message: '请选择活动区域', trigger: 'blur' },
            { min: 3, max: 111, message: '长度在 3 到 5 个字符', trigger: 'blur' }
          ]
        }
      };
    },
    // 声明周期页面加载时
    created:function(){
      // 商品分类的请求
      axios.get('http://localhost:8000/admin/shop/categories').then(res=>{
        //   console.log(res,'xxx');
        // this.options = res.data.data.products.data;
        this.options = res.data.data.categories
        // console.log(this.options,'option');
       
      })
      // 其他的商品描述请求
      let id = this.$route.params.id;// 请求当前路由的参数
      axios.get(`http://localhost:8000/admin/shop/products/${id}`).then(res=>{// 
        console.log(res,1);//打印当前请求到的 对象
        let data = res.data.data.product;
        this.ruleForm = {
          id:data.id,
          name:data.name,
          price:data.price,
          description:data.description,
          is_onsale:!!data.is_onsale,
          is_top:!!data.is_top,
          is_recommend:!!data.is_recommend,
          is_hot:!!data.is_hot,
          is_new:!!data.is_new,
          content:data.content,
          image:data.image,
          category_id:data.categories.map(item=>{
            return item.id
          })
        };  
       if (data.image == '' || data.image == null) {
            this.fileList = []
       }else{//  加载页面就显示缩略图,网络地址+本地地址
       this.fileList = [{name:this.ruleForm.image,url:this.img+data.image}]
       }
      })
    },
    methods: {
     // 提交修改
      submitForm(formName) {
        let id = this.$route.params.id;// 请求当前路由的参数
        this.$refs[formName].validate((valid) => {
          if (valid) {// 请求当前修改的值
              axios.put(`http://localhost:8000/admin/shop/products/${id}`,this.ruleForm).then(res=>{
                  this.$router.go(-1);// 返回上一级;
                  this.$message({
          message: '恭喜你，这是一条成功消息',
          type: 'success'
        });
              })
          } else {
            console.log('error submit!!');
            return false;
          }
        });
      },
      resetForm(formName) {
        this.$refs[formName].resetFields()
      }
      , // 上传图片规则
    beforeAvatarUpload(file) {
      console.log(file)
      const isJPG = file.type === 'image/jpeg' // 定义图片的格式
      const isPNG = file.type === 'image/png'
      const isGIF = file.type === 'image/gif'
      const isLt2M = file.size / 1024 / 1024 < 4 // 定义图片的大小

      if (!isJPG && !isPNG && !isGIF) {
        // 如果不等于当前的图片格式
        this.$message.error('上传头像图片只能是 JPG,png,gif 格式!') // 就返回提示
      }
      if (!isLt2M) {
        //如果大小超过了,就返回提示
        this.$message.error('上传头像图片大小不能超过 2MB!')
      }
      return isJPG || isPNG || (isGIF && isLt2M) //返回当前选择的图片和大小到页面
    },
    handleRemove(file, fileList) {
      console.log(file, fileList)
      this.ruleForm.image = ""; // 删除图片的时候,清空;以免提交重新赋值到数据库
    },
      // 新增  上传图片
    handlePictureCardPreview(file) {
      this.dialogImageUrl = file.url
      this.dialogVisible = true
    },
    handlePictureCardSuccess(response,file,fileList){
      console.log(response,3336);
      this.ruleForm.image = response.image;
    },
    jingao(){
      this.$message.error('只能上传一张图片!!!');
    },
    handlePictureCardPreview1(file) {
      this.dialogImageUrl1 = file.url;
      this.dialogVisible1 = true;
    },
     handleRemove1(file, fileList1) {
      console.log(file, fileList1);
      this.ruleForm.markdown_html_code = ""; // 删除图片的时候,清空;以免提交重新赋值到数据库
    },
     // 新增  上传图片
    handlePictureCardSuccess1(response, file, fileList1) {
      console.log(response, 333);
      // 得到图片的名字, 存到数据库
      // this.ruleForm.image = response.image;
      let url = response.image_url;
      this.ruleForm.markdown_html_code = `<p><img src="${url}" alt=""></img></p>`
    },
    }
  }
</script>

<style lang="scss" scoped>
.el-main{
    h4{
        margin:30px 0px
    }
}
.one{
  line-height: 0px;
  height:240px!important;
  margin-bottom: 70px;
}
</style>

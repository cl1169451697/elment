<template>
  <div>
    <!-- 面包屑导航 -->
    <el-breadcrumb separator="/">
      <el-breadcrumb-item :to="{ path: '/' }">首页</el-breadcrumb-item>
      <el-breadcrumb-item :to="{ path: '/shop/brands' }"
        >商城管理</el-breadcrumb-item
      >
      <el-breadcrumb-item :to="{ path: '/shop/products' }"
        >商品管理</el-breadcrumb-item
      >
      <el-breadcrumb-item>新增</el-breadcrumb-item>
    </el-breadcrumb>
    <h4 class="h4">新增管理</h4>

    <!-- tabs标签页 -->
    <el-form
      :model="ruleForm"
      :rules="rules"
      ref="ruleForm"
      label-width="100px"
      class="demo-ruleForm"
    >
      <el-tabs value="first">
          <!-- 导航栏一 -->
        <el-tab-pane label="通用信息" name="first">

            <!-- 商品名称 -->
          <el-form-item label="商品名称" prop="name">
            <el-input v-model="ruleForm.name"></el-input>
          </el-form-item>

          <!-- 商品分类 -->
          <el-form-item prop="category_id">
          <!-- 商品分类 -->
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

            <!-- 排序  -->
            <el-form-item label="单价" prop="price">
            <el-input v-model="ruleForm.price"></el-input>
             </el-form-item>

            <!-- 库存  -->
            <el-form-item label="库存" prop="stock">
            <el-input v-model="ruleForm.stock"></el-input>
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
              :on-exceed="jingao"
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

          <!-- 是否上架 -->
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
        <el-tab-pane label="商品介绍" name="second">
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

        <!-- 提交表单 -->
       <el-form-item>
            <el-button type="primary" @click="submitForm('ruleForm')">立即创建</el-button>
            <el-button @click="resetForm('ruleForm')">重置</el-button>
          </el-form-item>
    </el-form>
  </div>
</template>
<script>
/* 新增文本编辑器 */
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
      dialogImageUrl: "",
      dialogVisible: false,
      dialogImageUrl1: "",
      dialogVisible1: false,
      options: [],
      one:[],
      value: "",
      // 新增编辑信息页
      editorOption:{
        placeholder:'请填写商品介绍信息...'
      },
      ruleForm: {
        name: "",
        category_id: "",
        // is_show: true,
        price: "",
        stock: "",
        image: "",
        description:'',
        is_onsale:false,
        content:'',
        markdown_html_code:''
      },
      rules: {
        name: [
          { required: true, message: "请输入活动名称", trigger: "blur" },
          { min: 3, max: 5, message: "长度在 3 到 5 个字符", trigger: "blur" },
        ],
        // parent_id: [
        //   { required: true, message: '请选择分类', trigger: 'change' },
        // ],
      },
    };
  },
  created() {
    axios.get("http://localhost:8000/admin/shop/categories").then((res) => {
      console.log(res,111);
      let options = res.data.data.categories;
      this.options = options
      this.one = options
      console.log(this.options,222);
      
    });
  },
  methods: {
    submitForm(formName) {
      this.$refs[formName].validate((valid) => {
        if (valid) {
          axios
            .post("http://localhost:8000/admin/shop/products", this.ruleForm)
            .then((res) => {
              console.log(res);
              this.$router.go(-1);
              this.$message({
                message: "恭喜你，这是一条成功消息",
                type: "success",
              });
            });
        } else {
          console.log("error submit!!");
          return false;
        }
      });
    },
    resetForm(formName) {
      this.$refs[formName].resetFields();
    }, // 上传图片规则
    beforeAvatarUpload(file) {
      console.log(file);
      const isJPG = file.type === "image/jpeg"; // 定义图片的格式
      const isPNG = file.type === "image/png";
      const isGIF = file.type === "image/gif";
      const isLt2M = file.size / 1024 / 1024 < 4; // 定义图片的大小

      if (!isJPG && !isPNG && !isGIF) {
        // 如果不等于当前的图片格式
        this.$message.error("上传头像图片只能是 JPG,png,gif 格式!"); // 就返回提示
      }
      if (!isLt2M) {
        //如果大小超过了,就返回提示
        this.$message.error("上传头像图片大小不能超过 2MB!");
      }
      return isJPG || isPNG || (isGIF && isLt2M); //返回当前选择的图片和大小到页面
    },
    handleRemove(file, fileList) {
      // console.log(file, fileList);
      this.ruleForm.image = ""; // 删除图片的时候,清空;以免提交重新赋值到数据库
    },
    handlePictureCardPreview(file) {
      this.dialogImageUrl = file.url;
      this.dialogVisible = true;
    },
    // 新增  上传图片
    handlePictureCardSuccess(response, file, fileList) {
      console.log(response, 333);
      // 得到图片的名字, 存到数据库
      this.ruleForm.image = response.image;

      //方法二:截取
      // let arr = response.image_url.split('/')
      // let img = arr[arr.length-1]
      // this.ruleForm.image = mg;
    },
    jingao() {
      this.$message.error("只能上传一张图片!!!");
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
    
  },
};
</script>
<style lang="scss" scoped>
.h4 {
  margin: 15px 0px;
}
// .editor{
//   height: 200px;
// }
// .quill-editor .ql-snow{
//   height: 240px;
// }
.one{
  line-height: 0px;
  height:240px!important;
  margin-bottom: 70px;
}
// .one .ql-snow{
//   line-height: 0px;
//   height:240px!important;
//   // margin-bottom: 20px;
// }
</style>

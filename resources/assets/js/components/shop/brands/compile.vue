<!--  -->
<template>
<div>
  <el-breadcrumb separator="/">
  <el-breadcrumb-item :to="{ path: '/' }">首页</el-breadcrumb-item>
  <el-breadcrumb-item :to="{ path: '/shop/brands' }">商城管理</el-breadcrumb-item>
  <el-breadcrumb-item>商品品牌</el-breadcrumb-item>
  <el-breadcrumb-item >编辑</el-breadcrumb-item>
  </el-breadcrumb>
  <h4>{{bianji}}</h4>
  <div>
      <el-form :model="ruleForm" :rules="rules" ref="ruleForm" label-width="100px" class="demo-ruleForm">
  <el-form-item label="活动名称" prop="name">
    <el-input v-model="ruleForm.name"></el-input>
  </el-form-item>
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
   <el-form-item label="品牌网站" prop="url">
    <el-input v-model="ruleForm.url"></el-input>
  </el-form-item>
    <el-form-item label="排序" prop="sort_order">
    <el-input v-model="ruleForm.sort_order"></el-input>
  </el-form-item>
   <el-form-item label="是否显示" prop="is_show">
    <el-switch v-model="ruleForm.is_show"></el-switch>
  </el-form-item>
   <el-form-item label="品牌描述" prop="description">
    <el-input type="textarea" v-model="ruleForm.description"></el-input>
  </el-form-item>
  <el-form-item>
    <el-button type="primary" @click="submitForm('ruleForm')">提交</el-button>
    <el-button @click="resetForm('ruleForm')">重置</el-button>
  </el-form-item>
</el-form>
  </div>
</div>
</template>

<script>
  export default {
    data() {
      return {
        fileList: [],
      dialogImageUrl: '',
      dialogVisible: false,
        ruleForm:{},
          bianji:'编辑',
        rules: {
          name: [
            { required: true, message: '请输入活动名称', trigger: 'blur' },
            { min: 3, max: 5, message: '长度在 3 到 5 个字符', trigger: 'blur' }
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
      let id = this.$route.params.id;// 请求当前路由的参数
      axios.get(`http://localhost:8000/admin/shop/brands/${id}`).then(res=>{// 
        console.log(res);//打印当前请求到的 对象
        let data = res.data.data.brand;
        this.ruleForm = data;// 然后把请求到的值,赋值到this里面
       this.ruleForm.is_show = !!data.is_show;// 把开关按钮转换成布尔值,传到原来数据里
        this.fileList = [{url:data.image}]
        console.log(this.fileList,3111);
        
      })
      
    },
    methods: {
      submitForm(formName) {
        let id = this.$route.params.id;// 请求当前路由的参数
        this.$refs[formName].validate((valid) => {
          if (valid) {// 请求当前修改的值
              axios.put(`http://localhost:8000/admin/shop/brands/${id}`,this.ruleForm).then(res=>{
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
    },
    handlePictureCardPreview(file) {
      this.dialogImageUrl = file.url
      this.dialogVisible = true
    },
    handlePictureCardSuccess(response,file,fileList){
      console.log(response,333);
      this.ruleForm.image = response.image_url;
      
    },
    jingao(){
      this.$message.error('只能上传一张图片!!!');
    }
    }
  }
</script>

<style lang="scss" scoped>
.el-main{
    h4{
        margin:30px 0px
    }
}
</style>

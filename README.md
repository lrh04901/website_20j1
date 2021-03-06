<div align='right'><a href='https://gitee.com/schlibra/website_20j1'><img src='https://gitee.com/schlibra/website_20j1/widgets/widget_1.svg?color=c71d23' alt='Fork me on Gitee'></img></a></div>

[![star](https://gitee.com/schlibra/website_20j1/badge/star.svg?theme=white)](https://gitee.com/schlibra/website_20j1/stargazers)


# 20计1展示网站

网站用于展示班级以及班级内同学的风貌

[加入项目一起开发](https://gitee.com/schlibra/website_20j1/invite_link?invite=1d9215b7dfd9f7d81c484724b06f676a69bfe20df1a12785b7993663152babc5ebee344d1daf1f9c2f846e2b589479b3)

[后端文档手册](https://apidoc.gitee.com/schlibra/website_20j1)

## 项目架构
* system `代码目录`
    - core `动态页面路径`
        - argsTool.php `参数处理模块`
        - cookie.php `Cookie模块`
        - core.php `核心模块`
        - dbTool.php `数据库模块`
        - define.php `常量定义模块`
        - encryptTool.php `加密模块`
        - fileDBTool.php `文件数据库模块`
        - fileTool.php `文件处理模块`
        - get.php `GET模块`
        - language.php `语言模块`
        - mysqlTool.php `MySQL数据库模块`
        - post.php `POST模块`
        - redirect.php `重定向模块`
        - uploader.php `上传处理模块`
        - xcpak.php `更新包处理模块`
    - static `静态文件路径`
        - audio `音频目录`
            - ...
        - css `CSS路径`
            - classIntroduce.css `班级介绍页面样式文件`
            - index.css `首页的样式文件`
            - Page404.scss `404页面样式文件`
              - Page404.css `404页面样式文件`
            - update.css `更新页面样式文件`
            - webuploader.css `上传插件样式文件`
            - yule.css `娱乐页面样式文件`
            - zui.min.css `ZUI最小化样式文件`
            - zui.uploader.min.css `ZUI上传文件插件样式文件`
        - fonts `字体目录`
            - iceland.woff `冰岛字体`
            - zenicon.ttf `ZUI使用到的字体`
            - zenicon.woff `ZUI使用到的字体`
        - html `HTML路径`
            - classIntroduce.html `班级介绍文件`
            - error.html `错误页面文件`
            - head.html `头部文件`
            - index.html `首页的HTML文件`
            - nav.html `导航栏文件`
            - Page404.html `404页面文件`
            - setLang.html `设置语言页面文件`
            - update.html `更新页面文件`
            - yule.html `娱乐页面文件`
        - img `图片路径`
            - ...
            - j1logo.jpg `班级图标`
            - ...
        - js `JS路径`
            - index.js `首页的JS文件`
            - index2.js `首页的第二个JS文件`
            - jquery.cookie.min.js `jQuery cookie插件`
            - jquery.js `jQuery本地库`
            - update.js `更新页面脚本`
            - webuploader.min.js `上传插件`
            - zui.datatable.min.js `数据表插件`
            - zui.min.js `ZUI本地库`
            - zui.uploader.min.js `ZUI上传文件插件JS文件`
        - language `语言文件路径`
            - languages.json `语言列表文件`
            - en.lang `英文语言文件`
            - ja.lang `日语语言文件`
            - ko.lang `韩语语言文件`
            - zh-cn.lang `简体中文语言文件`
            - zh-tw.lang `繁体中文语言文件`
        - media `媒体文件路径`
            - .gitkeep  `空目录的占位文件`
* .editorconfig `规范代码风格文件`
* index.php `首页文件`
* ***README.md*** `自述文件`
* run.bat `调试时运行脚本`
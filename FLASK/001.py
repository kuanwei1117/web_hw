from flask import Flask, redirect, url_for
from flask import render_template,request
from model import User

app = Flask(__name__)

@app.route('/')
def index():
    return render_template('index.html')

@app.route('/login/',methods=['GET','POST'])
def user_login():
    return render_template('user_login.html')

@app.route('/regist/',methods=['GET','POST'])
def user_regist():
    if request.method == "POST":
        print(request.form)
        user = User()
        user.name = request.form["user_name"]
        user.pwd = request.form["user_pwd"]
        user.mail = request.form["user_mail"]
        user.age= request.form["user_age"]
        user.birthday = request.form["user_birthday"]
        user.face = request.form["user_face"]
        print(user.name)

        return redirect(url_for("user_login",username=user.name))
    return render_template('user_regist.html')

if __name__ == '__main__':
    app.run()

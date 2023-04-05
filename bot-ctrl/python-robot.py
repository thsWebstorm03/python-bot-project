from selenium import webdriver
from selenium.common.exceptions import NoSuchElementException
import time

from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC

from webdriver_manager.chrome import ChromeDriverManager

import websocket
import json
import datetime

from threading import Thread, Event
from time import sleep
import math
import rel


class Robot(Thread):
    def __init__(self, event, inputData, browser, ws, *args, **kwargs):
        super().__init__(*args, **kwargs)
        print('robot init')
        self.event = event
        self.inputData = json.loads(inputData)
        self.browser = browser
        self.ws = ws

    # run robot infinitely
    def run(self):
        try:
        
            while True:
                # Receive a message from the server
                time.sleep(0.5)
                if self.event.is_set():
                    self.browser.close()
                    print('The thread was stopped prematurely. robot stopped')
                    break
                result_json = self.inputData
                logs = []

                # self.ws.send("bot start=>running... ")
                time.sleep(0.01)
                
                for i in range(0, len(result_json)):
                    log_text = ""
                    email = result_json[i]['email']
                    password = result_json[i]['password']
                    name = result_json[i]['name']
                    passport = result_json[i]['passport']
                    latest_day = result_json[i]['latest_day']

                    print(name, passport, '{0} ->name and passport'.format(i))

                    # self.ws.send("new=>turn")
                    self.ws.send(json.dumps({'email' : email, 'name' : name, 'passport' : passport, 'log' : "new=>turn"}))
                    time.sleep(0.01)

                    # self.ws.send("email=>"+email)
                #  time.sleep(0.01)

                    # self.ws.send("name=>"+name)
                #  time.sleep(0.01)

                    # self.ws.send("passport=>"+passport)
                #  time.sleep(0.01)

                    # try sign-in
                    [isSigned, signIn_message] = self.sign_in(email, password)

                    if (isSigned):
                        log_text += signIn_message
                        print(log_text)
                        # self.ws.send("log=>"+signIn_message)
                        self.ws.send(json.dumps({'email' : email, 'name' : name, 'passport' : passport, 'log' : signIn_message}))
                        time.sleep(0.01)

                        # go to home
                        self.goHome()
                        log_text += "go to home\n "
                        # self.ws.send("log=>go to home")
                        self.ws.send(json.dumps({'email' : email, 'name' : name, 'passport' : passport, 'log' : "go to home\n "}))
                        time.sleep(0.01)

                        # match name and passport
                        [isExisted, NPMessage] = self.continueWithNameAndPassport(
                            name, passport)

                        if isExisted:

                            current_appointmentDate = NPMessage
                            log_text += "name and passport matching->found matching\n "
                            print(log_text)
                            # self.ws.send("log=>name and passport matching->found matching\n")
                            self.ws.send(json.dumps({'email' : email, 'name' : name, 'passport' : passport, 'log' : "name and passport matching->found matching\n "}))
                            time.sleep(0.01)

                            # click reschedule button
                            [isExistedReschedule, RMessage] = self.goReschedule()
                            if isExistedReschedule:

                                log_text += RMessage
                                print(log_text)
                                # self.ws.send("log =>"+RMessage)
                                self.ws.send(json.dumps({'email' : 'email', 'name' : name, 'passport' : passport, 'log' : RMessage}))
                                time.sleep(0.01)

                                # do reschedule
                                [isRescheduled, reschedule_message] = self.reschedule(
                                    latest_day, current_appointmentDate)

                                if isRescheduled:

                                    log_text += "do reschedule->new rescheduling done\n "
                                    print(log_text)
                                    # self.ws.send("log=>do reschedule->new rescheduling done\n ")
                                    self.ws.send(json.dumps({'email' : email, 'name' : name, 'passport' : passport, 'log' : "do reschedule->new rescheduling done\n "}))
                                    time.sleep(0.01)

                                    self.submitAndConfirm()
                                    log_text += 'submit and confirm->confirmed\n '
                                    # self.ws.send("log=>submit and confirm->confirmed\n ")
                                    self.ws.send(json.dumps({'email' : email, 'name' : name, 'passport' : passport, 'log' : 'submit and confirm->confirmed\n '}))
                                    time.sleep(0.01)

                                    self.successConfirm()
                                    log_text += 'success confirm =>confirmed\n '
                                    print(log_text)
                                    # self.ws.send("log=>success confirm->confirmed\n")
                                    self.ws.send(json.dumps({'email' : email, 'name' : name, 'passport' : passport, 'log' : 'success confirm =>confirmed\n '}))
                                    time.sleep(0.01)

                                    self.signOut()
                                    time.sleep(1)
                                    log_text += "sign-out->Signed Out\n"
                                    print(log_text)
                                    # self.ws.send("log=>sign-out->Signed Out\n")
                                    self.ws.send(json.dumps({'email' : email, 'name' : name, 'passport' : passport, 'log' : "sign-out->Signed Out\n"}))
                                    time.sleep(0.01)

                                    result_text = "summary->logDateTime : "+datetime.datetime.now().strftime('%Y-%m-%d %H:%M%S') + \
                                        "name : "+name + "locationName : " + \
                                        reschedule_message[0] + "scheduleDateTime : " + \
                                        reschedule_message[1]+"\n "
                                    log_text += result_text
                                    print(log_text)
                                    # self.ws.send("log=>"+result_text)
                                    self.ws.send(json.dumps({'email' : email, 'name' : name, 'passport' : passport, 'log' : result_text}))
                                    time.sleep(0.01)

                                else:
                                    log_text += reschedule_message
                                    print(log_text)
                                    # self.ws.send("log=>"+reschedule_message)
                                    self.ws.send(json.dumps({'email' : email, 'name' : name, 'passport' : passport, 'log' : reschedule_message}))
                                    time.sleep(0.01)

                                    self.signOut()
                                    time.sleep(1)
                                    log_text += "sign-out =>Signed Out\n"
                                    print(log_text)
                                    # self.ws.send("log=>sign-out->Signed Out\n")
                                    self.ws.send(json.dumps({'email' : email, 'name' : name, 'passport' : passport, 'log' : "Signed Out\n"}))
                                    time.sleep(0.01)

                            else:
                                log_text += RMessage
                                print(log_text)
                                # self.ws.send(RMessage)
                                self.ws.send(json.dumps({'email' : email, 'name' : name, 'passport' : passport, 'log' : RMessage}))
                                time.sleep(0.01)

                                self.signOut()
                                time.sleep(1)
                                log_text += "sign-out =>Signed Out\n"
                                print(log_text)
                                # self.ws.send("log=>sign-out->Signed Out\n")
                                self.ws.send(json.dumps({'email' : email, 'name' : name, 'passport' : passport, 'log' : "Signed Out\n"}))
                                time.sleep(0.01)

                        else:
                            log_text += NPMessage
                            print(log_text)
                            # self.ws.send("log=>" + NPMessage)
                            self.ws.send(json.dumps({'email' : email, 'name' : name, 'passport' : passport, 'log' : NPMessage}))
                            time.sleep(0.01)

                            self.signOut()
                            time.sleep(1)
                            log_text += "sign-out =>Signed Out\n"
                            print(log_text)
                            # self.ws.send("log=>sign-out->Signed Out\n")
                            self.ws.send(json.dumps({'email' : email, 'name' : name, 'passport' : passport, 'log' : "Signed Out\n"}))
                            time.sleep(0.01)

                    else:
                        log_text += signIn_message
                        print(log_text)
                        self.ws.send("log=>"+signIn_message)
                        self.ws.send(json.dumps({'email' : email, 'name' : name, 'passport' : passport, 'log' : signIn_message}))
                        time.sleep(0.01)


                    logs.append({'email': email, 'log': log_text})

                # Close the WebSocket connection
                print(logs, 'logs')
                # self.ws.send("T=>"+json.dumps(logs))
                # time.sleep(0.01)

                # self.ws.send("close bot=>closing")
                time.sleep(0.01)

        except KeyboardInterrupt:
            self.ws.close()
            self.browser.close()
            sys.exit()

    # sign in with email and password
    def sign_in(self,email, password):
        self.browser.get('https://ais.usvisa-info.com/en-il/niv/users/sign_in')
        try:
            WebDriverWait(self.browser, 10).until(
                EC.presence_of_element_located(
                    (By.XPATH, '//form[@class="simple_form new_user"]'))
            )

            self.browser.execute_script(
                'document.getElementById("sign_in_form").scrollIntoView(true)')

            form = self.browser.execute_script(
                'return document.getElementById("sign_in_form")')
            emailInput = self.browser.execute_script(
                'return document.getElementById("user_email")')
            passwordInput = self.browser.execute_script(
                'return document.getElementById("user_password")')
            acceptPolicyCheck = self.browser.execute_script(
                'return document.getElementsByClassName("icheck-item")[0]')
            submitBtn = self.browser.execute_script(
                "return document.querySelector(\'input[type=\"submit\"]')")

            emailInput.send_keys(email)
            passwordInput.send_keys(password)
            acceptPolicyCheck.click()
            submitBtn.click()

            time.sleep(3)
            isError = self.browser.execute_script(
                'if(document.querySelector("#sign_in_form p.error")) return false ; else return true;')
            if isError:
                return [True, 'sign-in->SignIn Success\n ']
            else:
                return [False, 'sign-in->SignIn Error\n ']
        except Exception as e:
            return [False, 'sign-in->SignIn Exception occured\n ']

    # go to home
    def goHome(self):
        self.browser.execute_script(
            'return document.querySelector(".nav-container ul li")').click()
        time.sleep(1)

    # find a matching of the given name and passport in the appointment list
    def continueWithNameAndPassport(self,name, passport):
        isExisted = False
        try:
            [dateAndTime, continueBtn] = self.browser.execute_script("const appointmentDiv = document.querySelectorAll('div.application');" +
                                                                "for(var i = 0; i < appointmentDiv.length; i++){" +
                                                                "if(appointmentDiv[i].getElementsByTagName('table')[0] == undefined) continue;"+
                                                                "const tableData = appointmentDiv[i].getElementsByTagName('table')[0].getInnerHTML();" +
                                                                "var flag = false;" +
                                                                f"if(tableData.indexOf(\"{name}\") != -1 && tableData.indexOf(\"{passport}\"))" +
                                                                "flag=true;" +
                                                                "if(flag){" +
                                                                "const appointText = appointmentDiv[i].querySelector('p.consular-appt').innerText;" +
                                                                "const match = appointText.match(/(\d{1,2}\s\w+\,\s\d{4}\,\s\d{2}\:\d{2})/);" +
                                                                "const dateAndTime = match ? match[1] : null;" +
                                                                "return [ dateAndTime, appointmentDiv[i].querySelector('li[role=\"menuitem\"]')];" +
                                                                "}" +
                                                                "}" +
                                                                "return ['', false];")
            if (continueBtn):
                isExisted = True
        except Exception as e:
            return [False, str(e)]

        if isExisted:
            continueBtn.click()
            time.sleep(2)
            return [True, dateAndTime]
        else:
            return [False, 'name and passport matching->No matching\n ']

    # reschedule step
    def goReschedule(self):

        accordion_title = self.browser.execute_script(
            ' return document.querySelectorAll(".accordion-title")[2].childNodes[1].innerText')
        if accordion_title == 'Reschedule Appointment':
            self.browser.execute_script(
                'return document.querySelectorAll(".accordion-title")[2]').click()
            time.sleep(1)
            self.browser.execute_script(
                'return document.querySelectorAll("div.accordion-content")[2].querySelector("a")').click()
            return [True, 'reschedule button->clicked\n ']
        else:
            return [False, 'reschedule button->No Reschedule\n ']

    # do rescheduling
    def reschedule(self,latest_day, cur_appoDate):
        temp = []
        location_length = self.browser.execute_script(
            'return document.querySelector("#appointments_consulate_appointment_facility_id").childElementCount')

        if location_length > 1:
            locationNameList = self.browser.execute_script('const location_list = document.querySelector("#appointments_consulate_appointment_facility_id");' +
                                                    'const locationNameList = [];' +
                                                    'for(var i = 0; i < location_list.length; i++){' +
                                                    'const locationName = location_list[i].text;' +
                                                    'locationNameList.push(locationName);}' +
                                                    'return locationNameList;')

            for locationIndex in range(1, len(locationNameList)):
                self.browser.execute_script(
                    'return document.querySelector("#appointments_consulate_appointment_facility_id")').click()
                time.sleep(1)

                self.browser.execute_script(
                    f'return document.querySelector("#appointments_consulate_appointment_facility_id")[{locationIndex}]').click()
                time.sleep(1)

                try:
                    self.browser.execute_script(
                        'return document.getElementById("appointments_consulate_appointment_date_input")').click()
                    time.sleep(0.1)
                except Exception as e:
                    return [False, 'no available appointements']

                while True:
                    activeDateItemList = self.browser.execute_script(
                        'return document.querySelectorAll(".ui-datepicker-group td[data-handler=\'selectDay\']")')

                    if len(activeDateItemList) > 0:
                        flag = False
                        flag1 = False
                        for i in range(0, len(activeDateItemList)):
                            dateTd = self.browser.execute_script(
                                f'return document.querySelectorAll(".ui-datepicker-group td[data-handler=\'selectDay\']")[{i}].firstChild')
                            date = dateTd.text
                            month = self.browser.execute_script(
                                f'return document.querySelectorAll(".ui-datepicker-group td[data-handler=\'selectDay\']")[{i}].getAttribute("data-month")')
                            year = self.browser.execute_script(
                                f'return document.querySelectorAll(".ui-datepicker-group td[data-handler=\'selectDay\']")[{i}].getAttribute("data-year")')

                            d_latest_day = datetime.datetime.strptime(
                                latest_day, '%Y-%m-%d')
                            d_item_day = datetime.datetime.strptime(
                                year+"-" + str(int(month)+1)+"-"+date, '%Y-%m-%d')
                            cur_appoDate = datetime.datetime.strptime(
                                cur_appoDate, "%d %B, %Y, %H:%M")

                            min_date = d_latest_day
                            if min_date > cur_appoDate:
                                min_date = cur_appoDate

                            if d_item_day >= datetime.datetime.strptime(min_date.strftime('%Y-%m-%d'), '%Y-%m-%d'):
                                flag1 = True
                                break

                            dateTd.click()

                            self.browser.execute_script(
                                'return document.querySelector("#appointments_consulate_appointment_time_input select")').click()
                            time.sleep(0.1)

                            time_option_length = self.browser.execute_script(
                                'return document.querySelector("#appointments_consulate_appointment_time_input select").length')

                            if time_option_length > 1:
                                time_option = self.browser.execute_script(
                                    'return document.querySelector("#appointments_consulate_appointment_time_input select")[1]')
                                time_option.click()
                                appointment_time = time_option.text
                                temp = [locationNameList[locationIndex], year+"-" +
                                        str(int(month)+1)+"-"+date+" "+appointment_time]
                                flag = True
                                break
                            else:
                                temp = False
                        if flag or flag1:
                            break

                    else:
                        self.browser.execute_script(
                            'return document.querySelector(".ui-datepicker-next")').click()
                        time.sleep(0.1)

                if len(temp):
                    return [True, temp]
                else:
                    return [False, 'do reschedule->No reschedule\n ']

            return [False, 'do reschedule->No matching latest date\n ']

        else:
            return [False, 'do reschedule = > Empty location list\n ']

    # submit and confirm
    def submitAndConfirm(self):
        self.browser.execute_script(
            'return document.getElementById("appointments_submit")').click()
        time.sleep(0.1)
        self.browser.execute_script(
            'return document.querySelector(".reveal-overlay div div a:nth-of-type(2)")').click()
    #  time.sleep(1)

    # last success confirm
    def successConfirm(self):
        self.browser.execute_script(
            'return document.querySelector(".ui-dialog-buttonset button.ui-button");').click()
        time.sleep(0.1)

    # signout
    def signOut(self):
        self.browser.execute_script(
            'return document.querySelectorAll(".nav-container ul.dropdown > li")[2]').click()
        time.sleep(0.1)
        self.browser.execute_script(
            'return document.querySelectorAll(".nav-container ul.dropdown > li")[2].querySelectorAll("ul.first-sub > li")[3]').click()
        time.sleep(0.1)

class Manage:
    def __init__(self, *args, **kwargs):
        # super().__init__(*args, **kwargs)
        print('Main Init')
        options = webdriver.ChromeOptions() 
        options.add_argument('headless') 
        self.browser = webdriver.Chrome(executable_path=ChromeDriverManager().install(), options=options)
        self.event = Event()
        self.thread = None

        # create a new Worker thread
        websocket.enableTrace(True)
        self.ws = websocket.WebSocketApp("ws://localhost:8080",
                              on_open= self.on_open,
                              on_message= self.on_message,
                              on_error= self.on_error,
                              on_close= self.on_close)
        self.ws.run_forever(dispatcher=rel, reconnect=5)  # Set dispatcher to automatic reconnection, 5 second reconnect delay if connection closed unexpectedly
        rel.signal(2, rel.abort)  # Keyboard Interrupt
        rel.dispatch()

    #socket error handler
    def on_error(self, ws, error):
        print(error)

    #socket close handler
    def on_close(self, ws, close_status_code, close_msg):
        print("### closed ###")

    #socket open handler
    def on_open(self, ws):
        print("Opened connection")

        # create a new Event object
        # self.event = Event()

        # initialData = [{'email': 'anat2081@gmail.com', 'password':'anat2081@gmail.com', 'name':'ORI SHARABI', 'passport': '36944299', 'latest_day': '2023-10-01'}]
        # # create a new Worker thread
        # self.thread = Robot(self.event, json.dumps(initialData), self.browser,ws)

        # # start the thread
        # self.thread.start()
        # print('thread started')

    def on_message(self, ws, message):
        print(message,'message arrived')

        # stop the child thread
        self.event.set()  

        # suspend  the thread after 3 seconds
        time.sleep(5)

        self.restart(message)
        # self.ws.close()
       
    def restart(self, data):
        options = webdriver.ChromeOptions() 
        options.add_argument('headless') 
        self.browser = webdriver.Chrome(executable_path=ChromeDriverManager().install(), options=options)

        # create a new Event object
        self.event = Event()

        # create a new Worker thread
        self.thread = Robot(self.event, data, self.browser, self.ws)
        
        # start the thread
        self.thread.start()
        print("restarted new robot")
    
def __main__():
    manage = Manage()

__main__()
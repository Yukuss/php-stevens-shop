import telebot
import mysql.connector
import random
from telebot import types

TOKEN = 'BotFatherTokenWhichIWouldNotShow123'
bot = telebot.TeleBot(TOKEN)

DB_CONFIG = {
    'host': 'localhost',
    'user': 'root',          
    'password': '',          
    'database': 'stevens',   
    'port': '3306'
}

def create_keyboard():
    keyboard = types.ReplyKeyboardMarkup(resize_keyboard=True, row_width=2)
    keyboard.add(
        types.KeyboardButton('📦 Товары'),
        types.KeyboardButton('🔍 Поиск'),
        types.KeyboardButton('🎲 Рандом'),
        types.KeyboardButton('📊 Наличие')
    )
    return keyboard

@bot.message_handler(commands=['start', 'help'])
def send_welcome(message):
    bot.reply_to(
        message,
        "Привет! Мегажоски бот 2.0 может:\n\n"
        "Показать все товары (/products)\n"
        "Найти товар по названию (/search)\n"
        "Прислать случайный товар (/random)\n"
        "Проверить наличие товара (/check)",
        reply_markup=create_keyboard()
    )

@bot.message_handler(func=lambda msg: msg.text in ['📦 Товары', '🔍 Поиск', '🎲 Рандом', '📊 Наличие'])
def handle_buttons(message):
    if message.text == '📦 Товары':
        show_products(message)
    elif message.text == '🔍 Поиск':
        ask_search_query(message)
    elif message.text == '🎲 Рандом':
        send_random_product(message)
    elif message.text == '📊 Наличие':
        ask_product_for_check(message)

@bot.message_handler(commands=['products'])
def show_products(message):
    try:
        conn = mysql.connector.connect(**DB_CONFIG)
        cursor = conn.cursor(dictionary=True)
        cursor.execute("SELECT name, price, quantity FROM products")
        products = cursor.fetchall()

        if not products:
            bot.reply_to(message, "Товаров пока нет", reply_markup=create_keyboard())
            return

        response = "<b>Все товары:</b>\n\n"
        for product in products:
            response += f"{product['name']} — <b>{product['price']} бун.</b>\n"

        bot.send_message(message.chat.id, response, parse_mode='HTML', reply_markup=create_keyboard())

    except Exception as e:
        bot.reply_to(message, f"Ошибка: {str(e)}", reply_markup=create_keyboard())
    finally:
        if conn.is_connected():
            conn.close()


@bot.message_handler(commands=['search'])
def ask_search_query(message):
    msg = bot.reply_to(message, "Введите название товара:", reply_markup=types.ReplyKeyboardRemove())
    bot.register_next_step_handler(msg, handle_search)

def handle_search(message):
    try:
        search_query = message.text.strip()
        conn = mysql.connector.connect(**DB_CONFIG)
        cursor = conn.cursor(dictionary=True)
        
        cursor.execute("SELECT name, price, quantity FROM products WHERE name LIKE %s", (f"%{search_query}%",))
        products = cursor.fetchall()

        if not products:
            bot.reply_to(message, f"Ничего не найдено по запросу «{search_query}»", reply_markup=create_keyboard())
            return

        response = f"<b>Результаты поиска («{search_query}»):</b>\n\n"
        for product in products:
            response += f"{product['name']} — <b>{product['price']} бун.</b>\n"

        bot.reply_to(message, response, parse_mode='HTML', reply_markup=create_keyboard())

    except Exception as e:
        bot.reply_to(message, f"Ошибка: {str(e)}", reply_markup=create_keyboard())
    finally:
        if conn.is_connected():
            conn.close()

@bot.message_handler(commands=['random'])
def send_random_product(message):
    try:
        conn = mysql.connector.connect(**DB_CONFIG)
        cursor = conn.cursor(dictionary=True)
        cursor.execute("SELECT name, price, quantity FROM products")
        products = cursor.fetchall()

        if not products:
            bot.reply_to(message, "Товаров нет", reply_markup=create_keyboard())
            return

        random_product = random.choice(products)
        status = "В наличии" if random_product['quantity'] > 0 else "Нет в наличии"
        
        response = (
            "<b>Случайный товар:</b>\n\n"
            f"<b>{random_product['name']}</b>\n"
            f"Цена: <b>{random_product['price']} бун.</b>\n"
        )
        
        bot.reply_to(message, response, parse_mode='HTML', reply_markup=create_keyboard())

    except Exception as e:
        bot.reply_to(message, f"Ошибка: {str(e)}", reply_markup=create_keyboard())
    finally:
        if conn.is_connected():
            conn.close()

@bot.message_handler(commands=['check'])
def ask_product_for_check(message):
    msg = bot.reply_to(
        message,
        "Введите назву товара:",
        reply_markup=types.ReplyKeyboardRemove()
    )
    bot.register_next_step_handler(msg, check_product_availability)

def check_product_availability(message):
    try:
        product_name = message.text.strip()
        conn = mysql.connector.connect(**DB_CONFIG)
        cursor = conn.cursor(dictionary=True)
        
        cursor.execute("SELECT name, price, quantity FROM products WHERE name LIKE %s", (f"%{product_name}%",))
        product = cursor.fetchone()

        if not product:
            bot.reply_to(
                message,
                f"Товар «{product_name}» не найден.",
                reply_markup=create_keyboard()
            )
            return

        if product['quantity'] > 0:
            response = (
                "<b>Товар в наличии:</b>\n\n"
                f"<b>{product['name']}</b>\n"
                f"Осталось <b>{product['quantity']} штучек</b>"
            )
        else:
            response = (
                "<b>Товар закончился:</b>\n\n"
                f"{product['name']}\n"
                f"Нет в наличии!"
            )

        bot.reply_to(
            message,
            response,
            parse_mode='HTML',
            reply_markup=create_keyboard()
        )

    except Exception as e:
        bot.reply_to(
            message,
            f"Ошибка: {str(e)}",
            reply_markup=create_keyboard()
        )
    finally:
        if conn.is_connected():
            conn.close()


@bot.message_handler(func=lambda message: True)
def handle_unknown(message):
    bot.reply_to(
        message,
        "Че?\n\n"
        "Используйте кнопки или команды:\n"
        "/products - все товары\n"
        "/search - поиск\n"
        "/random - случайный товар\n"
        "/check - проверка наличия",
        reply_markup=create_keyboard()
    )

if __name__ == '__main__':
    print("Бот запущен...")
    bot.polling()
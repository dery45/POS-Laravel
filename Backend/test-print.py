from PIL import Image

width = 384  # Width of the thermal paper (adjust as needed)
height = 240  # Height of the thermal paper (adjust as needed)

image = Image.new("1", (width, height), color=255)  # 1-bit monochrome image

from PIL import ImageFont, ImageDraw

draw = ImageDraw.Draw(image)

font_size = 12  # Adjust size as needed
font = ImageFont.truetype("consola.ttf", size=font_size)  # Specify the path to your desired font file

text = "Hello, World!"  # Replace with your desired text

x = 50  # X-coordinate of the text position
y = 50  # Y-coordinate of the text position

draw.text((x, y), text, font=font, fill=0)  # Fill 0 for black text

from escpos import printer

printer_instance = printer.Serial("USB001")  # Replace with the appropriate serial port of your thermal printer
printer_instance.image(image)
printer_instance.cut()
printer_instance.close()

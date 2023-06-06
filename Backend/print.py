from escpos.printer import Usb
import usb.core
import usb.util

def main():
    # USB vendor and product IDs
    vendor_id = 0x6868
    product_id = 0x0200

    # Find the printer device
    dev = usb.core.find(idVendor=vendor_id, idProduct=product_id)
    printer = Usb(vendor_id, product_id)

    if dev is None:
        raise ValueError("Printer not found.")

    try:

        # Initialize the printer
        printer.text("Hello, World!\n")
        printer.cut()

    finally:
        # Close the printer connection
        printer.close()

if __name__ == '__main__':
    main()

let selectedDevice = null;

function getPrinter() {
  if (!localStorage.printer) {
    console.error("Printer not set.");
    return new Error("Printer not set.");
  }
  return JSON.parse(localStorage.printer);
}

async function printToUSBPrinter(text) {
  let receiptText = text;
  console.log(receiptText);

  try {
    if (!localStorage.printer) {
      console.error("No USB printer selected.");
      return;
    }

    let printer = getPrinter();
    const devices = await navigator.usb.getDevices();

    const device = devices.find((device) => device.vendorId === printer.vendorId);
    if (device) {
      console.log("Found USB device:", device.productName);

      await device.open();
      await device.selectConfiguration(1);
      await device.claimInterface(0);

      const encoder = new TextEncoder();
      const data = encoder.encode(receiptText);
      await device.transferOut(1, data);

      console.log("Data sent to printer");
    } else {
      console.log("No USB device with the specified vendor ID found.");
      new FilamentNotification()
        .title("You should choose the printer first in printer settings.")
        .danger()
        .actions([
          new FilamentNotificationAction("Settings")
            .icon("heroicon-o-cog-6-tooth")
            .button()
            .url("/management/printer"),
        ])
        .send();
    }
  } catch (error) {
    console.error(error);
  }
}

function padText(text, length, alignRight = false, center = false, textSize = "normal") {
  const sizes = {
    normal: "\x1D\x21\x00",
    large: "\x1D\x21\x11",
  }[textSize];

  let paddedText = text;

  if (center) {
    const padLength = Math.max(0, length - text.length);
    const padStart = Math.floor(padLength / 2);
    const padEnd = Math.ceil(padLength / 2);
    paddedText = " ".repeat(padStart) + text + " ".repeat(padEnd);
  } else if (alignRight) {
    paddedText = text.padStart(length);
  } else {
    paddedText = text.padEnd(length);
  }

  return paddedText;
}

function moneyFormat(number, currency = null) {
  const formatter = new Intl.NumberFormat({
    style: "currency",
    currency: currency || "NGN", // Assuming USD as default
  });

  return formatter.format(number);
}

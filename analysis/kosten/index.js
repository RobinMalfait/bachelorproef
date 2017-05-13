const ms = require("ms");
const bytes = require("bytes");
// VALUES ARE GUESSES ATM!

/* Current situtation */
calculateStorageSpace({
  EVENT_SIZE: bytes("1 kb"),
  EVENT_COUNT: 5140,
  EVENT_COUNT_FOR: ms("1 day"),
  TIME_TO_STORE: ms("365 days")
});

/* Current situtation */
calculateStorageSpace({
  EVENT_SIZE: bytes("2 kb"),
  EVENT_COUNT: 51400,
  EVENT_COUNT_FOR: ms("1 day"),
  TIME_TO_STORE: ms("365 days")
});

/**
 * Utils
 */

function calculateStorageSpace({
  EVENT_SIZE,
  EVENT_COUNT,
  EVENT_COUNT_FOR,
  TIME_TO_STORE
}) {
  console.log(`Storing ${EVENT_COUNT} events for ${ms(TIME_TO_STORE)}`);

  const disk_space = bytes(
    EVENT_SIZE * (EVENT_COUNT / EVENT_COUNT_FOR) * TIME_TO_STORE
  );
  console.log(`Disk space required: ${disk_space}\n`);
}

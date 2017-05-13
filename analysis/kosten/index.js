const ms = require("ms");
const bytes = require("bytes");

const data = require("../read_write/data.json");
const write_request_count = Object.keys(data)
  .filter(type => ["post", "put", "patch", "delete"].includes(type))
  .reduce((total, key) => total += data[key], 0);

/* Current situtation */
// calculateStorageSpace({
//   EVENT_SIZE: bytes("168 b"),
//   EVENT_COUNT: write_request_count,
//   EVENT_COUNT_FOR: ms("30 days"),
//   TIME_TO_STORE: ms("5 years")
// });
calculateStorageSpace({
  EVENT_SIZE: bytes("2 kb"),
  EVENT_COUNT: 800000,
  EVENT_COUNT_FOR: ms("30 days"),
  TIME_TO_STORE: ms("50 years")
});

console.log(800000 / ms("30 days"));
console.log(26666.666666667 / ms("1 day"));
console.log(ms("50 years"));
console.log(ms("365.25 days") * 50);

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

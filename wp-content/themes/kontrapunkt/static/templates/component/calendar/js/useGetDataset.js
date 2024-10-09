export const useGetDataset = (element, dataContainer) => {
  if (!element) {
    throw new Error(`useGetDataset: Element not found`);
  }

  const data = {};

  for (const key in element.dataset) {
    if (key in dataContainer) {
      const value = element.dataset[key];
      let parsedValue = value;

      // Try to parse value as JSON if possible
      try {
        parsedValue = JSON.parse(value);
      } catch (e) {}

      // Check if the value type matches the dataContainer
      if (typeof dataContainer[key] === typeof parsedValue) {
        data[key] = parsedValue;
      } else {
        throw new Error(`useGetDataset: Invalid data type for key ${key}`);
      }
    }
  }

  // Check if any keys are missing from the dataset
  for (const key in dataContainer) {
    if (!(key in data)) {
      throw new Error(`useGetDataset: Key ${key} not found in dataset`);
    }
  }

  return data;
};
